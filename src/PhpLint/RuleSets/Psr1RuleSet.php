<?php
declare(strict_types=1);

namespace PhpLint\RuleSets;

use PhpLint\Rules;

class Psr1RuleSet extends AbstractRuleSet
{
    const RULE_SET_NAME = 'PSR-1';

    public function __construct()
    {
        parent::__construct(self::RULE_SET_NAME);

        $this->setRules([
            new Rules\ClassNamespaceRule()
        ]);
    }
}
