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
        return array_filter(
            $this->get(self::KEY_RULES) ?: [],
            function ($ruleConfig) {
                return self::getRuleSeverity($ruleConfig, true) !== self::RULE_SEVERITY_OFF;
            }
        );
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
        $severity = (is_string($ruleConfig)) ? $ruleConfig : $ruleConfig[0];
        if ($asName && !is_string($severity)) {
            return self::RULE_SEVERITIES[$severity];
        } elseif (!$asName && is_string($severity)) {
            return array_search($severity, self::RULE_SEVERITIES);
        }

        return $severity;
    }

    /**
     * @param Configuration $config
     * @return Configuration
     */
    public function mergeOntoConfig(Configuration $config): Configuration
    {
        return new self($this->values, $config);
    }
}
