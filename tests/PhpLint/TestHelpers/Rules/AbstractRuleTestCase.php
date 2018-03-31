<?php
declare(strict_types=1);

namespace PhpLint\TestHelpers\Rules;

use PHPUnit\Framework\TestCase;

abstract class AbstractRuleTestCase extends TestCase
{
    /**
     * @return RuleAssertionsDataProvider
     */
    abstract public function ruleAssertionsProvider(): RuleAssertionsDataProvider;

    /**
     * @dataProvider ruleAssertionsProvider
     *
     * @param AbstractRuleAssertion $ruleAssertion
     */
    public function testRule(AbstractRuleAssertion $ruleAssertion)
    {
        $ruleAssertion->assert();
    }
}
