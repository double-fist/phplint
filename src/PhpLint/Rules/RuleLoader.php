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
     * @param string $ruleId
     * @return Rule
     * @throws RuleException if the rule with the given $ruleId cannot not be found.
     */
    public function loadRule(string $ruleId): Rule
    {
        $lowercaseRuleId = mb_strtolower($ruleId);
        if (isset($this->loadedRules[$lowercaseRuleId])) {
            return $this->loadedRules[$lowercaseRuleId];
        }

        // Try to load the rule from the a configured plugin
        $rulePlugins = $this->config->getPlugins();
        foreach ($rulePlugins as $plugin) {
            if ($plugin->hasRule($ruleId)) {
                $this->loadedRules[$lowercaseRuleId] = $plugin->loadRule($ruleId);

                return $this->loadedRules[$lowercaseRuleId];
            }
        }

        // Try to load the rule from the default set
        $className = __NAMESPACE__ . '\\' . self::convertRuleIdToClassName($ruleId);
        if (!class_exists($className)) {
            throw RuleException::ruleNotFound($ruleId);
        }
        $this->loadedRules[$lowercaseRuleId] = new $className();

        return $this->loadedRules[$lowercaseRuleId];
    }

    /**
     * @param string $ruleId
     * @return string
     */
    public static function convertRuleIdToClassName(string $ruleId): string
    {
        $parts = [];
        preg_match_all('/[A-Za-z0-9]+/', $ruleId, $parts);
        $parts = array_map('mb_strtolower', $parts[0]);
        $parts = array_map('ucfirst', $parts);

        return implode('', $parts) . 'Rule';
    }
}
