<?php
declare(strict_types=1);

namespace PhpLint\Rules;

use PhpLint\Configuration\Configuration;

class RuleLoader
{
    /**
     * @var Configuration
     */
    private $config;

    /**
     * @var array
     */
    private $loadedRules = [];

    /**
     * @param Configuration $config
     */
    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $ruleName
     * @return Rule
     * @throws RuleException if the rule with the given $ruleName cannot not be found.
     */
    public function loadRule(string $ruleName): Rule
    {
        $lowercaseRuleName = mb_strtolower($ruleName);
        if (isset($this->loadedRules[$lowercaseRuleName])) {
            return $this->loadedRules[$lowercaseRuleName];
        }

        // Try to load the rule from the a configured plugin
        $rulePlugins = $this->config->getPlugins();
        foreach ($rulePlugins as $plugin) {
            if ($plugin->hasRule($ruleName)) {
                $this->loadedRules[$lowercaseRuleName] = $plugin->loadRule($ruleName);

                return $this->loadedRules[$lowercaseRuleName];
            }
        }

        // Try to load the rule from the default set
        $className = __NAMESPACE__ . '\\' . self::convertRuleNameToClassName($ruleName);
        if (!class_exists($className)) {
            throw RuleException::ruleNotFound($ruleName);
        }
        $this->loadedRules[$lowercaseRuleName] = new $className();

        return $this->loadedRules[$lowercaseRuleName];
    }

    /**
     * @param string $ruleName
     * @return string
     */
    public static function convertRuleNameToClassName(string $ruleName): string
    {
        $parts = [];
        preg_match_all('/[A-Za-z0-9]+/', $ruleName, $parts);
        $parts = array_map('mb_strtolower', $parts[0]);
        $parts = array_map('ucfirst', $parts);

        return implode('', $parts) . 'Rule';
    }
}
