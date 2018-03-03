<?php
declare(strict_types=1);

namespace PhpLint\RuleSets;

use PhpLint\Rules\Rule;

interface RuleSet
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return Rule[]
     */
    public function getRules(): array;
}
