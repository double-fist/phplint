<?php
declare(strict_types=1);

namespace PhpLint\Rules;

use PhpLint\Ast\AstNode;
use PhpLint\Ast\AstNodeType;
use PhpLint\Linter\LintContext;
use PhpLint\Linter\LintResult;

class ClassNamespaceRule extends AbstractRule
{
    const RULE_IDENTIFIER = 'class-namespace';
    const MESSAGE_ID_CLASS_NOT_NAMESPACED = 'classNotNamespaced';

    public function __construct()
    {
        $this->setDescription(
            RuleDescription::forRuleWithIdentifier(self::RULE_IDENTIFIER)
                ->explainedBy('Enforces that all classes must be contained in a PSR-4 namespace.')
                ->usingMessages([
                    self::MESSAGE_ID_CLASS_NOT_NAMESPACED => 'A class must be in a namespace of at least one level.',
                ])
                ->rejectsExamples([
                    RuleDescription::createPhpCodeExample('class AnyClass {}'),
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
        ];
    }

    /**
     * @inheritdoc
     */
    public function validate(AstNode $node, LintContext $context, LintResult $result)
    {
        $parentNode = $node->getParent();
        if (!$parentNode || $parentNode->getType() !== AstNodeType::NAMESPACE) {
            $result->reportViolation($node, self::MESSAGE_ID_CLASS_NOT_NAMESPACED);
        }
    }
}
