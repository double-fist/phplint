<?php
declare(strict_types=1);

namespace PhpLint\Rules;

use PhpLint\Ast\AstNode;
use PhpLint\Ast\AstNodeType;
use PhpLint\Ast\SourceContext;
use PhpLint\Linter\LintResult;

class SingleNamespaceInFileRule extends AbstractRule
{
    const RULE_IDENTIFIER = 'single-namespace-in-file';
    const MESSAGE_ID_MULTIPLE_NAMESPACE_DECLARATIONS_IN_FILE = 'multipleNamespaceDeclarationsInFile';

    public function __construct()
    {
        $this->setDescription(
            RuleDescription::forRuleWithIdentifier(self::RULE_IDENTIFIER)
                ->explainedBy('Enforces that each file must contain at most one \'namespace\' declaration.')
                ->usingMessages([
                    self::MESSAGE_ID_MULTIPLE_NAMESPACE_DECLARATIONS_IN_FILE => 'Each file must declare at most one namespace.',
                ])
                ->rejectsExamples([
                    RuleDescription::createPhpCodeExample(
                        'namespace PhpLint\Rules;',
                        'namespace PhpLint\Rules\Violation;'
                    ),
                    RuleDescription::createPhpCodeExample(
                        'namespace PhpLint\Rules;',
                        'class AnyClass {}',
                        'namespace PhpLint\Rules\Violation;',
                        'class AnyOtherClass {}'
                    ),
                ])
                ->acceptsExamples([
                    RuleDescription::createPhpCodeExample('class AnyClass {}'),
                    RuleDescription::createPhpCodeExample(
                        'namespace PhpLint\Rules;',
                        'class AnyClass {}'
                    ),
                ])
        );
    }

    /**
     * @inheritdoc
     */
    public function getTypes(): array
    {
        return [
            AstNodeType::NAMESPACE,
        ];
    }

    /**
     * @inheritdoc
     */
    public function validate(AstNode $node, SourceContext $context, $ruleConfig, LintResult $result)
    {
        // Check the AST for other namespace declarations before the given node
        $siblings =  ($node->getParent()) ? $node->getParent()->getChildren() : [];
        foreach ($siblings as $sibling) {
            if ($sibling->getType() !== AstNodeType::NAMESPACE) {
                continue;
            }

            if ($sibling !== $node) {
                $result->reportViolation(
                    $this->getName(),
                    RuleSeverity::getRuleSeverity($ruleConfig),
                    self::MESSAGE_ID_MULTIPLE_NAMESPACE_DECLARATIONS_IN_FILE,
                    $node
                );
            }

            break;
        }
    }
}
