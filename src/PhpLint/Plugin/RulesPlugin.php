<?php
declare(strict_types=1);

namespace PhpLint\Plugin;

use PhpLint\Rules\Rule;

/**
 * Implement this interface in a class named 'Rules' in the namespace PhpLint\Plugin\<VENDOR>\<NAME>, e.g.:
 *
 *  namespace PhpLint\Plugin\Acme\MyPlugin;
 *
 *  class Rules implements PhpLint\Plugin\RulesPlugin {}
 */
interface RulesPlugin extends Plugin
{
    /**
     * @return string[]
     */
    public function getPlugins(): array;

    /**
     * @return string[]
     */
    public function getRules(): array;

    /**
     * @param string $ruleName
     * @return bool
     */
    public function hasRule(string $ruleName): bool;

    /**
     * @param string $ruleName
     * @return Rule
     */
    public function loadRule(string $ruleName): Rule;
}
