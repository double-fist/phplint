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
interface RulesPlugin
{
    /**
     * The name must following the format '<VENDOR>/<NAME>' and correspond to the chosen namespace, e.g.:
     *
     *  Acme/MyPlugin
     *
     * @return string
     */
    public function getName(): string;

    /**
     * @return string[]
     */
    public function getRuleNames(): array;

    /**
     * @param string $ruleName
     * @param TODO $lintConfig
     * @return Rule
     */
    public function createRule(string $ruleName, $lintConfig): Rule;
}
