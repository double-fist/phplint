<?php
declare(strict_types=1);

namespace PhpLint\Rules;

use PhpLint\Linter\LintContext;
use PhpLint\Linter\LintResult;

class ClassNamespaceRule extends AbstractRule
{
    const RULE_IDENTIFIER = 'class-namespace-rule';

    public function __construct()
    {
        $this->setDescription(
            RuleDescription::forRuleWithIdentifier(self::RULE_IDENTIFIER)
                ->explainedBy('Enforces that all classes must be contained in a PSR-4 namespace.')
                ->rejectsExamples([
                    RuleDescription::createCodeExample('class AnyClass {}'),
                ])
                ->acceptsExamples([
                    RuleDescription::createCodeExample(
                        'namespace My\Namespace;',
                        'class AnyClass {}'
                    ),
                    RuleDescription::createCodeExample(
                        'namespace My\Namespace;',
                        'class AnyClass {}',
                        'namespace My\Namespace\Two;',
                        'class AnyOtherClass {}'
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
            'Stmt_Class',
        ];
    }

    /**
     * @inheritdoc
     */
    public function validate($node, LintContext $context, LintResult $result)
    {
        // TODO: Implement rule validation
    }
}
