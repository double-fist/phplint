<?php
declare(strict_types=1);

namespace PhpLint\Configuration;

class ConfigurationValidator
{
    /**
     * @param array $configData
     */
    public static function validateConfigData(array $configData)
    {
        foreach ($configData as $key => $value) {
            self::validateElement($key, $value);
        }
    }

    /**
     * @param string $key
     * @param mixed $value
     * @throws ConfigurationException if the given $key is invalid.
     */
    public static function validateConfigElement(string $key, $value)
    {
        switch ($key) {
            case Configuration::KEY_EXTENDS:
                self::validateExtends($value);
                break;
            case Configuration::KEY_PLUGINS:
                self::validatePlugins($value);
                break;
            case Configuration::KEY_ROOT:
                self::validateRoot($value);
                break;
            case Configuration::KEY_RULES:
                self::validateRules($value);
                break;
            case Configuration::KEY_SETTINGS:
                self::validateSettings($value);
                break;
            default:
                throw ConfigurationException::invalidKey($key);
        }
    }

    /**
     * An 'extends' value is valid, if it is either
     *
     *  - a string (length > 0) or
     *  - an array of strings.
     *
     * @param mixed $value
     * @throws ConfigurationException if the given $config contains an invalid 'extends' field.
     */
    public static function validateExtends($value)
    {
        // Check for string
        if (is_string($value) && mb_strlen($value) > 0) {
            return;
        }
        // Check for array of strings
        if (self::isSequentialArray($value) && count(array_filter($value, 'is_string')) === count($value)) {
            return;
        }

        throw new ConfigurationException('The config field "extends" must be either of type string or an array of strings.');
    }

    /**
     * A 'plugin' value is valid, if it is an array of strings.
     *
     * @param mixed $value
     * @throws ConfigurationException if the given $config contains an invalid 'plugins' field.
     */
    public static function validatePlugins($value)
    {
        // Check for string array
        if (self::isSequentialArray($value) && count(array_filter($value, 'is_string')) === count($value)) {
            return;
        }

        throw new ConfigurationException('The config field "plugins" must be an array of strings.');
    }

    /**
     * A 'root' value is valid, if it is a boolean.
     *
     * @param mixed $value
     * @throws ConfigurationException if the given $config contains an invalid 'root’ field.
     */
    public static function validateRoot($value)
    {
        // Check for bool
        if (is_bool($value)) {
            return;
        }

        throw new ConfigurationException('The config field "root" must be of type boolean.');
    }

    /**
     * A 'rules' value is valid, if it is either an empty array or an associative array whose keys are the name of the
     * rules and the values are the respective configurations. A rule configuration is valid, if it is either
     *
     *  - a valid severity (string or integer) or
     *  - a sequential array, containing at least a valid severity (string or integer) at index 0.
     *
     * @param mixed $value
     * @throws ConfigurationException if the given $config contains an invalid 'rules' field.
     */
    public static function validateRules($value)
    {
        // Check for empty array
        if (is_array($value) && count($value) === 0) {
            return;
        }

        // Check for associative array
        if (self::isAssocArray($value)) {
            $rulesValid = true;
            foreach ($value as $ruleName => $ruleConfig) {
                if (self::isRuleSeverity($ruleConfig) || self::isSequentialArray($ruleConfig) && count($ruleConfig) > 0 && self::isRuleSeverity($ruleConfig[0])) {
                    continue;
                }

                $rulesValid = false;
                break;
            }
            if ($rulesValid) {
                return;
            }
        }

        throw new ConfigurationException(
            'The config field "rules" must be of type array and contain associative arrays whose keys are the rules\'' .
            ' names and values are their severity (string) or a sequential array containing the severity as first' .
            ' element, followed by other, optional settings.'
        );
    }

    /**
     * @param mixed $value
     * @return bool
     */
    protected static function isRuleSeverity($value): bool
    {
        return is_string($value) && in_array($value, Configuration::RULE_SEVERITIES)
            || is_int($value) && in_array($value, array_keys(Configuration::RULE_SEVERITIES));
    }

    /**
     * A 'settings' value is valid, if it is an associative array (can be empty).
     *
     * @param mixed $value
     * @throws ConfigurationException if the given $config contains an invalid 'settings' field.
     */
    public static function validateSettings($value)
    {
        // Check for associative or empty array
        if (self::isAssocArray($value) || (is_array($value) && count($value) === 0)) {
            return;
        }

        throw new ConfigurationException('The config field "settings" must be an associative array.');
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function isSequentialArray($value): bool
    {
        return is_array($value) && !self::isAssocArray($value);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function isAssocArray($value): bool
    {
        return is_array($value) && count(array_filter(array_keys($value), 'is_string')) > 0;
    }
}
