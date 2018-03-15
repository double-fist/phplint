<?php
declare(strict_types=1);

namespace PhpLint\Rules;

class RuleSeverity
{
    const SEVERITY_OFF = 'off';
    const SEVERITY_WARNING = 'warning';
    const SEVERITY_ERROR = 'error';
    const ALL_SEVERITIES = [
        0 => self::SEVERITY_OFF,
        1 => self::SEVERITY_WARNING,
        2 => self::SEVERITY_ERROR,
    ];

    /**
     * @param mixed $value
     * @return bool
     */
    public static function isRuleSeverity($value): bool
    {
        return is_string($value) && in_array($value, self::ALL_SEVERITIES)
            || is_int($value) && in_array($value, array_keys(self::ALL_SEVERITIES));
    }

    /**
     * @param string|array $ruleConfig
     * @param bool $asName
     * @return string|int
     */
    public static function getRuleSeverity($ruleConfig, bool $asName = false)
    {
        $severity = (is_array($ruleConfig)) ? $ruleConfig[0] : $ruleConfig;
        if ($asName && !is_string($severity)) {
            return self::ALL_SEVERITIES[$severity];
        } elseif (!$asName && is_string($severity)) {
            return array_search($severity, self::ALL_SEVERITIES);
        }

        return $severity;
    }
}
