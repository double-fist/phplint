<?php
declare(strict_types=1);

namespace PhpLint\Configuration;

use PhpLint\Plugin\PluginLoader;

class ConfigurationLoader
{
    /**
     * @var PluginLoader
     */
    private $pluginLoader;

    /**
     * @param PluginLoader $pluginLoader
     */
    public function __construct(PluginLoader $pluginLoader)
    {
        $this->pluginLoader = $pluginLoader;
    }

    /**
     * Validates and loads the provided $configData into a Configuration. Any dependencies of the configuration
     * (via 'extends' or 'plugins') are resolved in the process. If the configuration extends other configurations,
     * they are added as parents of each other in the correct order. That is, the first extended config is parent of the
     * second, the second of the third etc. and the last extended config in the list is the parent of the loaded config.
     *
     * @param array $configData
     * @return Configuration
     * @throws ConfigurationException if the given $configData is invalid.
     */
    public function loadData(array $configData): Configuration
    {
        // Validate the config data first
        try {
            ConfigurationValidator::validateConfigData($configData);
        } catch (ConfigurationException $exception) {
            throw new ConfigurationException('The configuration data is invalid.', 0, $exception);
        }

        // Load any config plugins the given config extends and build the configuration hierarchy
        $parentConfig = null;
        if (isset($configData[Configuration::KEY_EXTENDS])) {
            $extends = $configData[Configuration::KEY_EXTENDS];
            if (is_string($extends)) {
                $extends = [$extends];
            }
            foreach ($extends as $configPluginName) {
                $plugin = $this->pluginLoader->loadPlugin($configPluginName, PluginLoader::PLUGIN_TYPE_CONFIG);
                $pluginConfig = $this->loadData($plugin->toArray());
                $parentConfig = ($parentConfig) ? $pluginConfig->mergeOntoConfig($parentConfig) : $pluginConfig;
            }
        }

        // Load any rule plugins the config depends on
        if (isset($configData[Configuration::KEY_PLUGINS])) {
            foreach ($configData[Configuration::KEY_PLUGINS] as $rulesPluginName) {
                $this->pluginLoader->loadPlugin($rulesPluginName, PluginLoader::PLUGIN_TYPE_RULES);
            }
        }

        // Merge a new config using the passed data onto the parent config, if possible
        $loadedConfig = new Configuration($configData, $parentConfig);
        if ($parentConfig) {
            $loadedConfig = $loadedConfig->mergeOntoConfig($parentConfig);
        }

        return $loadedConfig;
    }
}
