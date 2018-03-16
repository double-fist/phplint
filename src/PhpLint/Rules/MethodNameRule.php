<?php
declare(strict_types=1);

namespace PhpLint\Rules;

use PhpLint\Ast\SourceContext;
use PhpLint\Linter\LintResult;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;

class MethodNameRule extends AbstractRule
{
    const RULE_IDENTIFIER = 'method-name';
    const MESSAGE_ID_METHOD_NAME_NOT_IN_CAMEL_CASE = 'methodNameNotInCamelCase';

    public function __construct()
    {
        $this->setDescription(
            RuleDescription::forRuleWithIdentifier(self::RULE_IDENTIFIER)
                ->explainedBy('Enforces that all method names must be declared in \'camelCase\'.')
                ->usingMessages([
                    self::MESSAGE_ID_METHOD_NAME_NOT_IN_CAMEL_CASE => 'Method names must be declared in camelCase.',
                ])
                ->rejectsExamples([
                    RuleDescription::createPhpCodeExample(
                        'class AnyClass',
                        '{',
                        "\t" . 'public function MyMethod() {}',
                        '}'
                    ),
                    RuleDescription::createPhpCodeExample(
                        'class AnyClass',
                        '{',
                        "\t" . 'public function My_Method() {}',
                        '}'
                    ),
                    RuleDescription::createPhpCodeExample(
                        'class AnyClass',
                        '{',
                        "\t" . 'public function my_Method() {}',
                        '}'
                    ),
                ])
                ->acceptsExamples([
                    RuleDescription::createPhpCodeExample(
                        'class AnyClass',
                        '{',
                        "\t" . 'public function method() {}',
                        '}'
                    ),
                    RuleDescription::createPhpCodeExample(
                        'class AnyClass',
                        '{',
                        "\t" . 'public function myMethod() {}',
                        '}'
                    ),
                    RuleDescription::createPhpCodeExample(
                        'class AnyClass',
                        '{',
                        "\t" . 'public function myLongMethod() {}',
                        '}'
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
            ClassMethod::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function validate(Node $node, SourceContext $context, $ruleConfig, LintResult $result)
    {
        $methodName = $node->name;
        if (!$methodName || mb_strlen($methodName->name) === 0) {
            return;
        }

        $methodNamePattern = '/^[a-z][A-Za-z]*$/';
        if (preg_match($methodNamePattern, $methodName->name) !== 1) {
            $result->reportViolation(
                $this->getName(),
                RuleSeverity::getRuleSeverity($ruleConfig),
                self::MESSAGE_ID_METHOD_NAME_NOT_IN_CAMEL_CASE,
                $node
            );
        }
    }
}
