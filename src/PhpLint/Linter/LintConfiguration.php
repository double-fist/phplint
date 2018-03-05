<?php
declare(strict_types=1);

namespace PhpLint\Linter;

class LintConfiguration
{
    const KEY_ROOT = 'root';
    const KEY_RULES = 'rules';

    /**
     * @var array
     */
    private $values;

    /**
     * @var LintConfiguration|null
     */
    private $parentConfig = null;

    /**
     * @param array $values
     * @param LintConfiguration|null $parentConfig
     */
    public function __construct(array $values, LintConfiguration $parentConfig = null)
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
    public function isRootConfiguration(): bool
    {
        return isset($this->values[self::KEY_ROOT]) && $this->values[self::KEY_ROOT] === true;
    }

    /**
     * @param LintConfiguration $config
     * @return LintConfiguration
     */
    public function mergeOntoConfig(LintConfiguration $config): LintConfiguration
    {
        return new self($this->values, $config);
    }
}
