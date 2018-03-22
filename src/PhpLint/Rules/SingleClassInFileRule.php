<?php
declare(strict_types=1);

namespace PhpLint\Rules;

use PhpLint\Ast\Node\SourceRoot;
use PhpLint\Ast\NodeTraverser;
use PhpLint\Ast\SourceContext;
use PhpLint\Linter\LintResult;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;

class SingleClassInFileRule extends AbstractRule
{
    const RULE_IDENTIFIER = 'single-class-in-file';
    const MESSAGE_ID_MULTIPLE_CLASS_DECLARATIONS_IN_FILE = 'multipleClassDeclarationsInFile';

    public function __construct()
    {
        $this->setDescription(
            RuleDescription::forRuleWithIdentifier(self::RULE_IDENTIFIER)
                ->explainedBy('Enforces that each file contains at most one class declaration.')
                ->usingMessages([
                    self::MESSAGE_ID_MULTIPLE_CLASS_DECLARATIONS_IN_FILE => 'Each class must be in a file by itself.',
                ])
                ->rejectsExamples([
                    RuleDescription::createPhpCodeExample(
                        'class AnyClass {}',
                        'class AnyOtherClass {}'
                    ),
                    RuleDescription::createPhpCodeExample(
                        'namespace PhpLint\Rules;',
                        'class AnyClass {}',
                        'class AnyOtherClass {}'
                    ),
                    RuleDescription::createPhpCodeExample(
                        'namespace PhpLint\Rules;',
                        'class AnyClass {}',
                        'namespace PhpLint\OtherRules;',
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
            Class_::class,
        ];
    }

    /**
     * @inheritdoc
     * @throws RuleException if an unexpected node type is encountered.
     */
    public function validate(Node $node, SourceContext $context, $ruleConfig, LintResult $result)
    {
        // Check for a parent
        $parent = NodeTraverser::getParent($node);
        if (!$parent) {
            return;
        }

        // Make sure the parent is either a namespace or the source root, since in PHP class delcarations cannot be
        // nested in other class definitions (i.e. private classes)
        if (!($parent instanceof Namespace_) && !($parent instanceof SourceRoot)) {
            throw RuleException::unexpectedNodeTypeInRule(
                $this->getName(),
                $parent->getType(),
                [
                    'Stmt_Namespace',
                    'SourceRoot',
                ]
            );
        }

        // Collect all namespace (or source root) nodes in the file
        $rootNodes = [
            $parent,
        ];
        $parentSiblings = NodeTraverser::getSiblings($parent, true);
        if (count($parentSiblings) > 1) {
            // Parent must be a namespace, hence collect all namespaces in the file
            $rootNodes = array_filter(
                $parentSiblings,
                function (Node $parentSibling) {
                    return $parentSibling instanceof Namespace_;
                }
            );
        }

        // Collect all children of all root nodes, but keep only class declarations
        $children = array_map(
            function (Node $rootNode) {
                return NodeTraverser::getChildren($rootNode);
            },
            $rootNodes
        );
        $classNodes = array_values(array_filter(
            array_merge(...$children),
            function (Node $child) {
                return $child instanceof Class_;
            }
        ));

        // Try to find a violation by finding any other class that is delcared before (above) the given class node. If
        // such a class declaration exists, the given node violates the rule.
        if (array_search($node, $classNodes) !== 0) {
            $result->reportViolation(
                $this,
                RuleSeverity::getRuleSeverity($ruleConfig),
                self::MESSAGE_ID_MULTIPLE_CLASS_DECLARATIONS_IN_FILE,
                $context->getSourceRangeOfNode($node)->getStart(),
                $context
            );
        }
    }
}
