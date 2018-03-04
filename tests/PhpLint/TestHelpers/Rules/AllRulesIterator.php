<?php
declare(strict_types=1);

namespace PhpLint\TestHelpers\Rules;

use ArrayIterator;
use PhpLint\Rules;
use PhpLint\Rules\Rule;

class AllRulesIterator extends ArrayIterator
{
    /**
     * @var Rule[]
     */
    private static $rules = null;

    /**
     * @return Rule[]
     */
    protected static function getRules(): array
    {
        if (self::$rules !== null) {
            return self::$rules;
        }

        // Add any rules that shall be tested below
        self::$rules = [
            new  Rules\ClassConstantNameRule(),
            new  Rules\ClassNameRule(),
            new  Rules\ClassNamespaceRule(),
            new  Rules\MethodNameRule(),
            new  Rules\SingleClassInFileRule(),
            new  Rules\SingleNamespaceInFileRule(),
        ];

        return self::$rules;
    }

    /**
     * @inheritdoc
     */
    public function __construct()
    {
        $groupedRuleTests = array_map(
            function (Rule $rule): array {
                return self::createRuleTests($rule);
            },
            self::getRules()
        );
        $mergedRuleTests = array_merge(...$groupedRuleTests);

        parent::__construct($mergedRuleTests);
    }

    /**
     * @param Rule $rule
     * @return AbstractRuleAssertion[]
     */
    private static function createRuleTests(Rule $rule): array
    {
        $acceptanceTests = array_map(
            function (string $testCode) use ($rule) {
                return [
                    new RuleAcceptanceAssertion($rule, $testCode)
                ];
            },
            $rule->getDescription()->getAcceptedExamples()
        );

        $rejectionTests = array_map(
            function (string $testCode) use ($rule) {
                return [
                    new RuleRejectionAssertion($rule, $testCode)
                ];
            },
            $rule->getDescription()->getRejectedExamples()
        );

        $fixableExamples = $rule->getDescription()->getFixableExamples();
        $fixTests = array_map(
            function (string $testCode) use ($rule) {
                return [
                    new RuleFixAssertion($rule, $testCode, $fixableExamples[$testCode])
                ];
            },
            array_keys($fixableExamples)
        );

        return array_merge($acceptanceTests, $rejectionTests, $fixTests);
    }
}
