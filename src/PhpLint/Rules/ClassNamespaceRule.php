<?php
declare(strict_types=1);

namespace PhpLint\Rules;

use PhpLint\Ast\AstNode;
use PhpLint\Ast\AstNodeType;
use PhpLint\Linter\LintContext;
use PhpLint\Linter\LintResult;

class ClassNamespaceRule extends AbstractRule
{
    const RULE_IDENTIFIER = 'class-namespace-rule';
    const MESSAGE_ID_CLASS_NOT_NAMESPACED = 'classNotNamespaces';
    const MESSAGE_ID_MULTIPLE_NAMESPACE_DEFINITIONS = 'multipleNamespaceDefinitions';

    public function __construct()
    {
        $this->setDescription(
            RuleDescription::forRuleWithIdentifier(self::RULE_IDENTIFIER)
                ->explainedBy('Enforces that all classes must be contained in a PSR-4 namespace.')
                ->usingMessageIds([
                    self::MESSAGE_ID_CLASS_NOT_NAMESPACED,
                    self::MESSAGE_ID_MULTIPLE_NAMESPACE_DEFINITIONS,
                ])
                ->rejectsExamples([
                    RuleDescription::createPhpCodeExample('class AnyClass {}'),
                    RuleDescription::createPhpCodeExample(
                        'namespace PhpLint\Rules;',
                        'class AnyClass {}',
                        'namespace PhpLint\Rules\Violation;',
                        'class AnyOtherClass {}'
                    ),
                ])
                ->acceptsExamples([
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
            AstNodeType::CLASS_DECLARATION,
            AstNodeType::SOURCE_ROOT,
        ];
    }

    /**
     * @inheritdoc
     */
    public function validate(AstNode $node, LintContext $context, LintResult $result)
    {
        switch ($node->getType()) {
            case AstNodeType::CLASS_DECLARATION:
                $this->checkForClassNamespace($node, $context, $result);
                break;
            case AstNodeType::SOURCE_ROOT:
                $this->checkForMultipleNamespaces($node, $context, $result);
                break;
        }
    }

    private function checkForClassNamespace(AstNode $node, LintContext $context, LintResult $result)
    {
        $parentNode = $node->getParent();
        if (!$parentNode || $parentNode->getType() !== AstNodeType::NAMESPACE) {
            $result->reportViolation($node, self::MESSAGE_ID_CLASS_NOT_NAMESPACED);
        }
    }

    private function checkForMultipleNamespaces(AstNode $node, LintContext $context, LintResult $result)
    {
        $foundFirstNamespaceNode = false;
        foreach ($node->getChildren() as $child) {
            if ($child->getType() !== AstNodeType::NAMESPACE) {
                continue;
            }

            if ($foundFirstNamespaceNode) {
                // nth 'namespace' node
                $result->reportViolation($node, self::MESSAGE_ID_MULTIPLE_NAMESPACE_DEFINITIONS);
            } else {
                // First 'namespace' node
                $foundFirstNamespaceNode = true;
            }
        }
    }
}
