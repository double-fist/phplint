<?php
declare(strict_types=1);

namespace PhpLint\Linter\Directive;

use PhpLint\Ast\SourceContext;
use PhpLint\Ast\SourceLocation;
use PhpLint\Ast\Token;

class DirectiveParser
{
    /**
     * @var SourceContext
     */
    private $sourceContext;

    /**
     * @var Directive[]|null
     */
    private $directives = null;

    /**
     * @var DisableDirective[]|null
     */
    private $disableDirectives = null;

    /**
     * @param SourceContext $sourceContext
     */
    public function __construct(SourceContext $sourceContext)
    {
        $this->sourceContext = $sourceContext;
    }

    /**
     * @return Directive[]
     */
    public function getDirectives(): array
    {
        if ($this->directives !== null) {
            return $this->directives;
        }

        // Find all comment tokens
        $commentTokenName = token_name(\T_COMMENT);
        $commentTokens = array_filter(
            $this->sourceContext->getTokens(),
            function (Token $token) use ($commentTokenName) {
                return $token->getType() === $commentTokenName;
            }
        );

        // Extract the directives from the comment tokens
        $this->directives = array_values(array_filter(array_map(
            function (Token $commentToken) {
                return self::findDirectiveInCommentToken($commentToken);
            },
            $commentTokens
        )));

        // Make sure the directives are sorted by location (even though the tokens are iterated from start to end of
        // the source context)
        usort(
            $this->directives,
            function (Directive $lhs, Directive $rhs) {
                return $lhs->getSourceLocation()->compare($rhs->getSourceLocation());
            }
        );

        return $this->directives;
    }

    /**
     * @return DisableDirective[]
     */
    public function getDisableDirectives(): array
    {
        if ($this->disableDirectives !== null) {
            return $this->disableDirectives;
        }

        // Split all disable directives into one directive per enabled/disabled rule
        $disableDirectives = array_map(
            function (Directive $directive) {
                $ruleIds = (empty($directive->getValue())) ? [null] : self::splitDirectiveValue($directive->getValue());

                return array_map(
                    function ($ruleId) use ($directive) {
                        return new DisableDirective(
                            $directive->getType(),
                            SourceLocation::atLineAndColumn(
                                $directive->getSourceLocation()->getLine(),
                                $directive->getSourceLocation()->getColumn()
                            ),
                            $ruleId
                        );
                    },
                    $ruleIds
                );
            },
            $this->getDirectives()
        );
        $disableDirectives = (count($disableDirectives) > 0) ? array_merge(...$disableDirectives) : [];

        // Unwrap disable (next) line directives into block directives
        $this->disableDirectives = self::unwrapDisableLineDirectives($disableDirectives);

        // Sort all disable directives by location
        usort(
            $this->disableDirectives,
            function (DisableDirective $lhs, DisableDirective $rhs) {
                return $lhs->getSourceLocation()->compare($rhs->getSourceLocation());
            }
        );

        return $this->disableDirectives;
    }

    /**
     * @param Token $commentToken
     * @return Directive|null
     */
    protected static function findDirectiveInCommentToken(Token $commentToken)
    {
        // Extract the content
        $content = trim($commentToken->getValue());
        $isBlockComment = false;
        if (mb_strpos($content, '/*') === 0) {
            // Block comment, hence remove of both the opening and closing tags
            $content = trim(mb_substr($content, 2, -2));
            $isBlockComment = true;
        } else {
            // Trailing, singe line comment, hence only remove the opening tag
            $content = trim(mb_substr($content, 2));
        }

        // Try to find a valid directive and split of its value
        $matches = [];
        if (preg_match('/^(phplint(-\w+){0,3})(\s|$)/', $content, $matches) !== 1) {
            return null;
        }
        $directiveValue = mb_substr($content, (mb_strlen($matches[1]) + 1));
        $directiveValue = trim(preg_replace('/--.*/', '', $directiveValue));
        $commentLocation = $commentToken->getSourceRange();
        $isSingleLineComment = !$isBlockComment || $commentLocation->getStart()->getLine() === $commentLocation->getEnd()->getLine();
        // Line disabling directives must only be used in single line comments
        if ($isSingleLineComment && preg_match('/^phplint-disable-(next-)?line$/', $matches[1]) === 1) {
            return new Directive(
                mb_substr($matches[1], mb_strlen('phplint-')),
                $commentLocation->getStart(),
                $directiveValue
            );
        } elseif ($isBlockComment) {
            switch ($matches[1]) {
                case 'phplint-disable':
                    return new Directive(
                        DisableDirective::TYPE_DISABLE,
                        $commentLocation->getStart(),
                        $directiveValue
                    );
                case 'phplint-enable':
                    return new Directive(
                        DisableDirective::TYPE_ENABLE,
                        $commentLocation->getStart(),
                        $directiveValue
                    );
                default:
                    return null;
            }
        }

        return null;
    }

    /**
     * @param string $configString
     * @return string[]
     */
    protected static function splitDirectiveValue(string $configString): array
    {
        $configParts = preg_split('/,+/', preg_replace('/\s*,\s*/', ',', $configString));
        if ($configParts === false) {
            return [];
        }
        $configParts = array_map('trim', $configParts);
        $configParts = array_values(array_filter($configParts));

        return $configParts;
    }

    /**
     * @param DisableDirective[] $disableDirectives
     * @return DisableDirective[]
     */
    protected static function unwrapDisableLineDirectives(array $disableDirectives): array
    {
        // Split into block and line directives
        $blockDirectives = array_values(array_filter(
            $disableDirectives,
            function (DisableDirective $directive) {
                return $directive->getType() === DisableDirective::TYPE_DISABLE
                    || $directive->getType() === DisableDirective::TYPE_ENABLE;
            }
        ));
        $lineDirectives = array_values(array_filter(
            $disableDirectives,
            function (DisableDirective $directive) {
                return $directive->getType() === DisableDirective::TYPE_DISABLE_LINE
                    || $directive->getType() === DisableDirective::TYPE_DISABLE_NEXT_LINE;
            }
        ));

        // Convert the line directives to block directives disabling that specific line
        foreach ($lineDirectives as $directive) {
            switch ($directive->getType()) {
                case DisableDirective::TYPE_DISABLE_LINE:
                    $blockDirectives[] = new DisableDirective(
                        DisableDirective::TYPE_DISABLE,
                        SourceLocation::atLineAndColumn($directive->getSourceLocation()->getLine(), 1),
                        $directive->getRuleId()
                    );
                    $blockDirectives[] = new DisableDirective(
                        DisableDirective::TYPE_ENABLE,
                        SourceLocation::atLineAndColumn($directive->getSourceLocation()->getLine() + 1, 0),
                        $directive->getRuleId()
                    );
                    break;
                case DisableDirective::TYPE_DISABLE_NEXT_LINE:
                    $blockDirectives[] = new DisableDirective(
                        DisableDirective::TYPE_DISABLE,
                        SourceLocation::atLineAndColumn($directive->getSourceLocation()->getLine() + 1, 1),
                        $directive->getRuleId()
                    );
                    $blockDirectives[] = new DisableDirective(
                        DisableDirective::TYPE_ENABLE,
                        SourceLocation::atLineAndColumn($directive->getSourceLocation()->getLine() + 2, 0),
                        $directive->getRuleId()
                    );
                    break;
                default:
                    break;
            }
        }

        return $blockDirectives;
    }
}
