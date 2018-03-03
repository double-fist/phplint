<?php
declare(strict_types=1);

namespace PhpLint\Test\Rules;

use PhpLint\TestHelpers\Rules\AbstractRuleAssertion;
use PhpLint\TestHelpers\Rules\AllRulesIterator;
use PHPUnit\Framework\TestCase;

class AllRulesTest extends TestCase
{
    /**
     * @dataProvider provider
     *
     * @param AbstractRuleAssertion $ruleTest
     */
    public function testRule(AbstractRuleAssertion $ruleTest)
    {
        $ruleTest->assert();
    }

    /**
     * @return AllRulesIterator
     */
    public function provider(): AllRulesIterator
    {
        return new AllRulesIterator();
    }
}
