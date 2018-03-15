<?php
declare(strict_types=1);

namespace PhpLint\Rules;

use PhpLint\Ast\AstNode;
use PhpLint\Ast\AstNodeType;
use PhpLint\Ast\SourceContext;
use PhpLint\Linter\LintResult;

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
            AstNodeType::CLASS_DECLARATION,
        ];
    }

    /**
     * @inheritdoc
     */
    public function validate(AstNode $node, SourceContext $context, $ruleConfig, LintResult $result)
    {
        // Check the AST for other class declarations before the given node
        $siblings =  ($node->getParent()) ? $node->getParent()->getChildren() : [];
        foreach ($siblings as $sibling) {
            if ($sibling->getType() !== AstNodeType::CLASS_DECLARATION) {
                continue;
            }

            if ($sibling !== $node) {
                $result->reportViolation(
                    $this->getName(),
                    RuleSeverity::getRuleSeverity($ruleConfig),
                    self::MESSAGE_ID_MULTIPLE_CLASS_DECLARATIONS_IN_FILE,
                    $node
                );
            }

            break;
        }
    }
}
