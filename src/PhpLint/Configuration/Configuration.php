<?php
declare(strict_types=1);

namespace PhpLint\Configuration;

class Configuration
{
    /**
     * Valid config keys as supported by this class.
     */
    const KEY_EXTENDS = 'extends';
    const KEY_PLUGINS = 'plugins';
    const KEY_ROOT = 'root';
    const KEY_RULES = 'rules';
    const KEY_SETTINGS = 'settings';

    /**
     * Valid rule severity names as supported by this class.
     */
    const RULE_SEVERITY_OFF = 'off';
    const RULE_SEVERITY_WARNING = 'warning';
    const RULE_SEVERITY_ERROR = 'error';
    const RULE_SEVERITIES = [
        0 => self::RULE_SEVERITY_OFF,
        1 => self::RULE_SEVERITY_WARNING,
        2 => self::RULE_SEVERITY_ERROR,
    ];

    /**
     * @var array
     */
    private $values;

    /**
     * @var Configuration|null
     */
    private $parentConfig = null;

    /**
     * @param array $values
     * @param Configuration|null $parentConfig
     */
    public function __construct(array $values, Configuration $parentConfig = null)
    {
        $this->values = $values;
        $this->parentConfig = $parentConfig;
    }

    /**
     * @return Configuration|null
     */
    public function getParentConfig()
    {
        return $this->parentConfig;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        return (isset($this->values[$key])) ? $this->values[$key] : null;
    }

    /**
     * @return array
     */
    public function getExtends(): array
    {
        $extends = $this->get(self::KEY_EXTENDS) ?: [];
        if (is_string($extends)) {
            $extends = [$extends];
        }

        return $extends;
    }

    /**
     * @return array
     */
    public function getPlugins(): array
    {
        return $this->get(self::KEY_PLUGINS) ?: [];
    }

    /**
     * @return bool
     */
    public function isRoot(): bool
    {
        return $this->get(self::KEY_ROOT) === true;
    }

    /**
     * By default this method only returns the names of the rules whose severity is not 'off'. Pass true as first
     * argument to get all rules.
     *
     * @param bool $allRules
     * @return array
     */
    public function getRules(bool $allRules = false): array
    {
        $rules = $this->get(self::KEY_RULES) ?: [];
        if (!$allRules) {
            $rules = array_filter(
                $rules,
                function ($ruleConfig) {
                    return self::getRuleSeverity($ruleConfig, true) !== self::RULE_SEVERITY_OFF;
                }
            );
        }

        return $rules;
    }

    /**
     * @return array
     */
    public function getSettings(): array
    {
        return $this->get(self::KEY_SETTINGS) ?: [];
    }

    /**
     * @param string|array $ruleConfig
     * @param bool $asName
     * @return string
     */
    public static function getRuleSeverity($ruleConfig, bool $asName = false): string
    {
        $severity = (is_array($ruleConfig)) ? $ruleConfig[0] : $ruleConfig;
        if ($asName && !is_string($severity)) {
            return self::RULE_SEVERITIES[$severity];
        } elseif (!$asName && is_string($severity)) {
            return array_search($severity, self::RULE_SEVERITIES);
        }

        return $severity;
    }

    /**
     * Merges the values of this config as well as of the given $baseConfig and uses the resulting data to create a
     * new config instance, which is returned. The values of this config always always take precendence over the
     * $baseConfig's values.
     *
     * @param Configuration $baseConfig
     * @return Configuration
     */
    public function mergeOntoConfig(Configuration $baseConfig): Configuration
    {
        $mergedConfigData = [];

        // Merge the extended configs
        $mergedConfigData[self::KEY_EXTENDS] = array_values(array_unique(array_merge(
            $baseConfig->getExtends(),
            $this->getExtends()
        )));

        // Merge the required plugins
        $mergedConfigData[self::KEY_PLUGINS] = array_values(array_unique(array_merge(
            $baseConfig->getPlugins(),
            $this->getPlugins()
        )));

        // Set 'root' if at least of the configs is root
        $mergedConfigData[self::KEY_ROOT] = $this->isRoot() || $baseConfig->isRoot();

        // Merge the rules by using the base config's rules as base. Any rules only required by this config are added.
        // If a rule is required in both configs, the severity and rule config found in this config are used. That is,
        // if the base config specifies a rule config while this config only sets the severtiy of the same rule, the
        // original config is preserved and only its severity is changed.
        $mergedRules = $baseConfig->getRules(true);
        foreach ($this->getRules(true) as $ruleName => $ruleConfig) {
            if (!isset($mergedRules[$ruleName])) {
                // Add new rule
                $mergedRules[$ruleName] = $ruleConfig;
            } elseif (is_array($mergedRules[$ruleName]) && is_string($ruleConfig)) {
                // Only change the severity
                $mergedRules[$ruleName][0] = $ruleConfig;
            } else {
                // Overwrite the whole rule config
                $mergedRules[$ruleName] = $ruleConfig;
            }
        }
        $mergedConfigData[self::KEY_RULES] = $mergedRules;

        // Merge the settings of both configs, using their keys as the only merge level. That is, we don't use a
        // recursive merge here even though the settings could be many levels deep.
        $mergedConfigData[self::KEY_SETTINGS] = array_merge(
            $baseConfig->getSettings(),
            $this->getSettings()
        );

        return new self($mergedConfigData, $baseConfig);
    }
}
