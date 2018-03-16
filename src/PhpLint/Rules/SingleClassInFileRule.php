<?php
declare(strict_types=1);

namespace PhpLint\Rules;

use PhpLint\Ast\AstNodeTraverser;
use PhpLint\Ast\SourceContext;
use PhpLint\Linter\LintResult;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;

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
     */
    public function validate(Node $node, SourceContext $context, $ruleConfig, LintResult $result)
    {
        $parent = AstNodeTraverser::getParent($node);
        if (!$parent) {
            return;
        }

        // Check the AST for other class declarations BEFORE the given node, which is why we explicitly traverse the
        // parents children and stop once we reached the given node without finding a violation.
        $siblings = AstNodeTraverser::getChildren($parent);
        foreach ($siblings as $sibling) {
            if (!($sibling instanceof Class_)) {
                continue;
            }

            if ($sibling === $node) {
                // Node is first namespace
                return;
            }

            // Found class before the given node
            $result->reportViolation(
                $this->getName(),
                RuleSeverity::getRuleSeverity($ruleConfig),
                self::MESSAGE_ID_MULTIPLE_CLASS_DECLARATIONS_IN_FILE,
                $node
            );

            break;
        }
    }
}
