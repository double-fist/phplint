<?php
declare(strict_types=1);

namespace PhpLint\Test\Configuration;

use PhpLint\Configuration\Configuration;
use PhpLint\Configuration\ConfigurationValidator;
use PHPUnit\Framework\TestCase;

class ConfigurationValidatorTest extends TestCase
{
    /**
     * Tests the validation of an key/value config entries.
     *
     * @expectedException PhpLint\Configuration\ConfigurationException
     */
    public function testUnknownKeyIsInvalid()
    {
        $key = 'anyInvalidKey';
        $value = [];
        ConfigurationValidator::validateConfigElement($key, $value);
    }

    /**
     * Tests the validation of 'extends' values.
     */
    public function testStringIsValidExtendsValue()
    {
        $value = 'Vendor/ConfigName';
        ConfigurationValidator::validateExtends($value);
        self::assertTrue(true);
    }

    /**
     * Tests the validation of 'extends' values.
     */
    public function testArrayOfStringsIsValidExtendsValue()
    {
        $value = [
            'Vendor/ConfigName',
            'OtherVendor/ConfigName',
        ];
        ConfigurationValidator::validateExtends($value);
        self::assertTrue(true);
    }

    /**
     * Tests the validation of 'extends' values.
     */
    public function testEmptyArrayIsValidExtendsValue()
    {
        $value = [];
        ConfigurationValidator::validateExtends($value);
        self::assertTrue(true);
    }

    /**
     * Tests the validation of 'extends' values.
     *
     * @expectedException PhpLint\Configuration\ConfigurationException
     */
    public function testEmptyStringIsInvalidExtendsValue()
    {
        $value = '';
        ConfigurationValidator::validateExtends($value);
    }

    /**
     * Tests the validation of 'extends' values.
     *
     * @expectedException PhpLint\Configuration\ConfigurationException
     */
    public function testMixedArrayIsInvalidExtendsValue()
    {
        $value = [
            10,
            'Vendor/ConfigName',
        ];
        ConfigurationValidator::validateExtends($value);
    }

    /**
     * Tests the validation of 'extends' values.
     *
     * @expectedException PhpLint\Configuration\ConfigurationException
     */
    public function testAssociativeArrayIsInvalidExtendsValue()
    {
        $value = [
            'Vendor' => 'ConfigName',
        ];
        ConfigurationValidator::validateExtends($value);
    }

    /**
     * Tests the validation of 'extends' values.
     *
     * @expectedException PhpLint\Configuration\ConfigurationException
     */
    public function testNumberIsInvalidExtendsValue()
    {
        $value = 10;
        ConfigurationValidator::validateExtends($value);
    }

    /**
     * Tests the validation of 'extends' values.
     *
     * @expectedException PhpLint\Configuration\ConfigurationException
     */
    public function testBoolIsInvalidExtendsValue()
    {
        $value = true;
        ConfigurationValidator::validateExtends($value);
    }

    /**
     * Tests the validation of 'plugins' values.
     */
    public function testArrayOfStringsIsValidPluginsValue()
    {
        $value = [
            'Vendor/PluginName',
            'OtherVendor/PluginName',
        ];
        ConfigurationValidator::validatePlugins($value);
        self::assertTrue(true);
    }

    /**
     * Tests the validation of 'plugins' values.
     */
    public function testEmptyArrayIsValidPluginsValue()
    {
        $value = [];
        ConfigurationValidator::validatePlugins($value);
        self::assertTrue(true);
    }

    /**
     * Tests the validation of 'plugins' values.
     *
     * @expectedException PhpLint\Configuration\ConfigurationException
     */
    public function testMixedArrayIsInvalidPluginsValue()
    {
        $value = [
            10,
            'Vendor/PluginName',
        ];
        ConfigurationValidator::validatePlugins($value);
    }

    /**
     * Tests the validation of 'plugins' values.
     *
     * @expectedException PhpLint\Configuration\ConfigurationException
     */
    public function testAssociativeArrayIsInvalidPluginsValue()
    {
        $value = [
            'Vendor' => 'PluginName',
        ];
        ConfigurationValidator::validatePlugins($value);
    }

    /**
     * Tests the validation of 'plugins' values.
     *
     * @expectedException PhpLint\Configuration\ConfigurationException
     */
    public function testStringIsInvalidPluginsValue()
    {
        $value = 'Vendor/PluginName';
        ConfigurationValidator::validatePlugins($value);
    }

    /**
     * Tests the validation of 'plugins' values.
     *
     * @expectedException PhpLint\Configuration\ConfigurationException
     */
    public function testNumberIsInvalidPluginsValue()
    {
        $value = 10;
        ConfigurationValidator::validatePlugins($value);
    }

    /**
     * Tests the validation of 'plugins' values.
     *
     * @expectedException PhpLint\Configuration\ConfigurationException
     */
    public function testBoolIsInvalidPluginsValue()
    {
        $value = true;
        ConfigurationValidator::validatePlugins($value);
    }

    /**
     * Tests the validation of 'root' values.
     */
    public function testTrueIsValidRootValue()
    {
        $value = true;
        ConfigurationValidator::validateRoot($value);
        self::assertTrue(true);
    }

    /**
     * Tests the validation of 'root' values.
     */
    public function testFalseIsValidRootValue()
    {
        $value = false;
        ConfigurationValidator::validateRoot($value);
        self::assertTrue(true);
    }

    /**
     * Tests the validation of 'root' values.
     *
     * @expectedException PhpLint\Configuration\ConfigurationException
     */
    public function testEmptyArrayIsInvalidRootValue()
    {
        $value = [];
        ConfigurationValidator::validateRoot($value);
    }

    /**
     * Tests the validation of 'root' values.
     *
     * @expectedException PhpLint\Configuration\ConfigurationException
     */
    public function testSequentialArrayIsInvalidRootValue()
    {
        $value = [
            10,
            20,
        ];
        ConfigurationValidator::validateRoot($value);
    }

    /**
     * Tests the validation of 'root' values.
     *
     * @expectedException PhpLint\Configuration\ConfigurationException
     */
    public function testAssociativeArrayIsInvalidRootValue()
    {
        $value = [
            'a' => 10,
            'b' => 20,
        ];
        ConfigurationValidator::validateRoot($value);
    }

    /**
     * Tests the validation of 'root' values.
     *
     * @expectedException PhpLint\Configuration\ConfigurationException
     */
    public function testStringIsInvalidRootValue()
    {
        $value = 'true';
        ConfigurationValidator::validateRoot($value);
    }

    /**
     * Tests the validation of 'root' values.
     *
     * @expectedException PhpLint\Configuration\ConfigurationException
     */
    public function testNumberIsInvalidRootValue()
    {
        $value = 10;
        ConfigurationValidator::validateRoot($value);
    }

    /**
     * Tests the validation of 'rules' values.
     */
    public function testEmptyArrayIsValidRulesValue()
    {
        $value = [];
        ConfigurationValidator::validateRules($value);
        self::assertTrue(true);
    }

    /**
     * Tests the validation of 'rules' values.
     */
    public function testAssociativeArrayWithSeverityStringValuesIsValidRulesValue()
    {
        $value = [
            'off-rule' => Configuration::RULE_SEVERITY_OFF,
            'warning-rule' => Configuration::RULE_SEVERITY_WARNING,
            'error-rule' => Configuration::RULE_SEVERITY_ERROR,
        ];
        ConfigurationValidator::validateRules($value);
        self::assertTrue(true);
    }

    /**
     * Tests the validation of 'rules' values.
     */
    public function testAssociativeArrayWithSeverityNumberValuesIsValidRulesValue()
    {
        $value = [
            'off-rule' => array_search(Configuration::RULE_SEVERITY_OFF, Configuration::RULE_SEVERITIES),
            'warning-rule' => array_search(Configuration::RULE_SEVERITY_WARNING, Configuration::RULE_SEVERITIES),
            'error-rule' => array_search(Configuration::RULE_SEVERITY_ERROR, Configuration::RULE_SEVERITIES),
        ];
        ConfigurationValidator::validateRules($value);
        self::assertTrue(true);
    }

    /**
     * Tests the validation of 'rules' values.
     */
    public function testAssociativeArrayWithSequentialArrayValuesIsValidRulesValue()
    {
        $value = [
            'off-rule' => [
                Configuration::RULE_SEVERITY_OFF,
            ],
            'warning-rule' => [
                Configuration::RULE_SEVERITY_WARNING,
                'my-value',
            ],
            'error-rule' => [
                Configuration::RULE_SEVERITY_ERROR,
                10,
                true,
                [],
            ],
        ];
        ConfigurationValidator::validateRules($value);
        self::assertTrue(true);
    }

    /**
     * Tests the validation of 'rules' values.
     *
     * @expectedException PhpLint\Configuration\ConfigurationException
     */
    public function testSequentialArrayIsInvalidRulesValue()
    {
        $value = [
            'first-rule',
            'second-rule',
        ];
        ConfigurationValidator::validateRules($value);
    }

    /**
     * Tests the validation of 'rules' values.
     *
     * @expectedException PhpLint\Configuration\ConfigurationException
     */
    public function testStringIsInvalidRulesValue()
    {
        $value = 'rule';
        ConfigurationValidator::validateRules($value);
    }

    /**
     * Tests the validation of 'rules' values.
     *
     * @expectedException PhpLint\Configuration\ConfigurationException
     */
    public function testNumberIsInvalidRulesValue()
    {
        $value = 10;
        ConfigurationValidator::validateRules($value);
    }

    /**
     * Tests the validation of 'rules' values.
     *
     * @expectedException PhpLint\Configuration\ConfigurationException
     */
    public function testBoolIsInvalidRulesValue()
    {
        $value = true;
        ConfigurationValidator::validateRules($value);
    }

    /**
     * Tests the validation of 'rules' values.
     *
     * @expectedException PhpLint\Configuration\ConfigurationException
     */
    public function testAssociativeArrayWithUnknownSeverityStringValueIsInvalidRulesValue()
    {
        $value = [
            'my-rule' => 'custom severity',
        ];
        ConfigurationValidator::validateRules($value);
    }

    /**
     * Tests the validation of 'rules' values.
     *
     * @expectedException PhpLint\Configuration\ConfigurationException
     */
    public function testAssociativeArrayWithUnknownSeverityNumberValueIsInvalidRulesValue()
    {
        $value = [
            'my-rule' => count(Configuration::RULE_SEVERITIES),
        ];
        ConfigurationValidator::validateRules($value);
    }

    /**
     * Tests the validation of 'settings' values.
     */
    public function testEmptyArrayIsValidSettingsValue()
    {
        $value = [];
        ConfigurationValidator::validateSettings($value);
        self::assertTrue(true);
    }

    /**
     * Tests the validation of 'settings' values.
     */
    public function testAssociativeArrayIsValidSettingsValue()
    {
        $value = [
            'a' => 10,
            'b' => 20,
            'c' => [
                'd',
            ],
        ];
        ConfigurationValidator::validateSettings($value);
        self::assertTrue(true);
    }

    /**
     * Tests the validation of 'settings' values.
     *
     * @expectedException PhpLint\Configuration\ConfigurationException
     */
    public function testSequentialArrayIsInvalidSettingsValue()
    {
        $value = [
            10,
            20,
        ];
        ConfigurationValidator::validateSettings($value);
    }

    /**
     * Tests the validation of 'settings' values.
     *
     * @expectedException PhpLint\Configuration\ConfigurationException
     */
    public function testStringIsInvalidSettingsValue()
    {
        $value = 'true';
        ConfigurationValidator::validateSettings($value);
    }

    /**
     * Tests the validation of 'settings' values.
     *
     * @expectedException PhpLint\Configuration\ConfigurationException
     */
    public function testNumberIsInvalidSettingsValue()
    {
        $value = 10;
        ConfigurationValidator::validateSettings($value);
    }

    /**
     * Tests the validation of 'settings' values.
     *
     * @expectedException PhpLint\Configuration\ConfigurationException
     */
    public function testBoolIsInvalidSettingsValue()
    {
        $value = true;
        ConfigurationValidator::validateSettings($value);
    }

    /**
     * Tests the detection of sequential arrays.
     */
    public function testIsSequentialArray()
    {
        $value = [
            10,
            20,
        ];
        self::assertTrue(ConfigurationValidator::isSequentialArray($value));

        // PHP converts number-like string keys to integers, hence the following is a sequential array
        $value = [
            '0' => 10,
            '1' => 20,
        ];
        self::assertTrue(ConfigurationValidator::isSequentialArray($value));

        // Empty array is considered sequential
        $value = [];
        self::assertTrue(ConfigurationValidator::isSequentialArray($value));

        $value = [
            'a' => 10,
            'b' => 20,
        ];
        self::assertFalse(ConfigurationValidator::isSequentialArray($value));

        $value = 'a string';
        self::assertFalse(ConfigurationValidator::isSequentialArray($value));

        $value = 10;
        self::assertFalse(ConfigurationValidator::isSequentialArray($value));

        $value = true;
        self::assertFalse(ConfigurationValidator::isSequentialArray($value));
    }

    /**
     * Tests the detection of associative arrays.
     */
    public function testIsAssocArray()
    {
        $value = [
            'a' => 10,
            'b' => 20,
        ];
        self::assertTrue(ConfigurationValidator::isAssocArray($value));

        // PHP converts number-like string keys to integers, hence the following is not an associative array
        $value = [
            '0' => 10,
            '1' => 20,
        ];
        self::assertFalse(ConfigurationValidator::isAssocArray($value));

        $value = [
            10,
            20,
        ];
        self::assertFalse(ConfigurationValidator::isAssocArray($value));

        // Empty array is not considered associative
        $value = [];
        self::assertFalse(ConfigurationValidator::isAssocArray($value));

        $value = 'a string';
        self::assertFalse(ConfigurationValidator::isAssocArray($value));

        $value = 10;
        self::assertFalse(ConfigurationValidator::isAssocArray($value));

        $value = true;
        self::assertFalse(ConfigurationValidator::isAssocArray($value));
    }
}
