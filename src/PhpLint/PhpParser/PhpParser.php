<?php
declare(strict_types=1);

namespace PhpLint\PhpParser;

use PhpLint\AbstractParser;
use PhpLint\Ast\Node\SourceRoot;
use PhpLint\Ast\NodeTraverser;
use PhpLint\Ast\SourceContext;
use PhpLint\Ast\SourceLocation;
use PhpLint\Ast\SourceRange;
use PhpLint\Ast\Token;
use PhpParser\Lexer;
use PhpParser\Node;
use PhpParser\ParserFactory;

class PhpParser extends AbstractParser
{
    /** @var \PhpParser\Parser $phpParser */
    private $phpParser;

    private $lexer;

    public function __construct()
    {
        $parserFactory = new ParserFactory();
        $this->lexer = new Lexer(
            [
                'usedAttributes' => [
                    'comments',
                    'startLine',
                    'endLine',
                    'startTokenPos',
                    'endTokenPos',
                    'startFilePos',
                    'endFilePos',
                ],
            ]
        );
        $this->phpParser = $parserFactory->create(ParserFactory::PREFER_PHP7, $this->lexer);
    }

    public function parse(string $code, string $path = null): SourceContext
    {
        $parserResult = $this->phpParser->parse($code);

        $astRoot = new SourceRoot($parserResult);
        NodeTraverser::createParentBackLinks($astRoot);

        $tokens = $this->transformTokens($this->lexer->getTokens());

        return new ParserContext(
            $astRoot,
            $tokens,
            $code,
            $path
        );
    }

    /**
     * @param array $tokens
     * @return Token[]
     */
    private function transformTokens($tokens): array
    {
        $line = 1;
        $column = 1;

        $transformedTokens = [];

        foreach ($tokens as $token) {
            if (is_string($token)) {
                $transformedTokens[] = new Token(
                    'T_CHARACTER',
                    $token,
                    SourceRange::between(
                        SourceLocation::atLineAndColumn($line, $column),
                        SourceLocation::atLineAndColumn($line, $column)
                    )
                );
                $column += 1;
            } else {
                $tokenType = $token[0];
                $tokenValue = $token[1];
                $line = $token[2];

                // Compute the number of extra lines spanned by the token (a 2-line token spans 1 line)
                $extraSpannedLines = mb_substr_count($tokenValue, "\n") ?: 0;

                // Compute the end column in a multiline-safe manner
                $partFromLastNewline = mb_strrchr($tokenValue, "\n");
                $endColumn = $partFromLastNewline ? mb_strlen($partFromLastNewline) - 1 : $column + mb_strlen($tokenValue) - 1;
                $transformedTokens[] = new Token(
                    token_name($tokenType),
                    $tokenValue,
                    SourceRange::between(
                        SourceLocation::atLineAndColumn($line, $column),
                        SourceLocation::atLineAndColumn($line + $extraSpannedLines, $endColumn)
                    )
                );

                $line += $extraSpannedLines;
                $column = $endColumn + 1;
            }
        }

        return $transformedTokens;
    }
}
