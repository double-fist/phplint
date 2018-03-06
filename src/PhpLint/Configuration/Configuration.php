<?php
declare(strict_types=1);

namespace PhpLint\Configuration;

class Configuration
{
    const KEY_EXTENDS = 'extends';
    const KEY_PLUGINS = 'plugins';
    const KEY_ROOT = 'root';
    const KEY_RULES = 'rules';
    const KEY_SETTINGS = 'settings';

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
        if (isset($this->values[$key])) {
            return $this->values[$key];
        }

        if ($this->parentConfig) {
            return $this->parentConfig->get($key);
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isRootConfig(): bool
    {
        return self::isTrue(self::KEY_ROOT);
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
