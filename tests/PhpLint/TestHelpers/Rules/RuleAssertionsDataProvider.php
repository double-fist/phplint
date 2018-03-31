<?php
declare(strict_types=1);

namespace PhpLint\TestHelpers\Rules;

use ArrayIterator;
use PhpLint\Rules\Rule;

class RuleAssertionsDataProvider extends ArrayIterator
{
    /**
     * @param Rule[] $rules
     */
    public function __construct(array $rules)
    {
        $groupedRuleTests = array_map(
            function (Rule $rule): array {
                return self::createRuleTests($rule);
            },
            $rules
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
