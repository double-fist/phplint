<?php
declare(strict_types=1);

namespace PhpLint\Plugin;

use PhpLint\Configuration\ConfigurationValidator;

class PluginLoader
{
    const PLUGIN_TYPE_CONFIG = 'Config';
    const PLUGIN_TYPE_RULES = 'Rules';

    /**
     * @var array
     */
    private $loadedPlugins = [
        self::PLUGIN_TYPE_CONFIG => [],
        self::PLUGIN_TYPE_RULES => [],
    ];

    /**
     * Recursively loads the plugin having the given $pluginName and $pluginType as well as the plugins it depends on.
     *
     * @param string $pluginName
     * @param string $pluginType
     * @param string[] $dependentPlugins
     * @return Plugin
     * @throws PluginException if the given $pluginType is invalid or a circular dependency between any two plugins is detected.
     */
    public function loadPlugin(string $pluginName, string $pluginType, array $dependentPlugins = []): Plugin
    {
        if (!isset($this->loadedPlugins[$pluginType])) {
            throw PluginException::invalidPluginType($pluginType);
        }
        if (isset($this->loadedPlugins[$pluginType][$pluginName])) {
            return $this->loadedPlugins[$pluginType][$pluginName];
        }

        // Load the plugin and check for circular dependencies
        $plugin = $this->loadPluginNamespace($pluginName, $pluginType);
        ConfigurationValidator::validateConfigData($plugin->toArray());
        $circularDependencies = array_intersect($dependentPlugins, $plugin->getDependencies());
        if (count($circularDependencies) > 0) {
            throw PluginException::circularDependecies($pluginName, $circularDependecies);
        }

        // Load all dependencies
        $updatedDependentPlugins = array_merge($dependentPlugins, [$pluginName]);
        $configPlugins  = ($plugin instanceof ConfigPlugin) ? $plugin->getExtends() : [];
        foreach ($plugin->getDependencies() as $dependencyName) {
            $pluginType = (in_array($dependencyName, $configPlugins)) ? self::PLUGIN_TYPE_CONFIG : self::PLUGIN_TYPE_RULES;
            $this->loadPlugin($dependencyName, $pluginType, $updatedDependentPlugins);
        }

        // Cache loaded plugin
        $this->loadedPlugins[$pluginType][$pluginName] = $plugin;

        return $plugin;
    }

    /**
     * @param string $pluginName
     * @param string $className
     * @return Plugin
     * @throws PluginException if the plugin's namespace does not exist or the name defined in the plugin does not match its namespace.
     */
    protected function loadPluginNamespace(string $pluginName, string $className): Plugin
    {
        // Check plugin namespace
        $pluginNamespace = 'PhpLint\\Plugin\\' . str_replace('/', '\\', $pluginName);
        $className = $pluginNamespace . '\\' . $className;
        if (!class_exists($className)) {
            throw PluginException::pluginNamespaceNotFound($pluginName, $pluginNamespace);
        }

        // Validate plugin
        $plugin = new $className();
        if ($plugin->getName() !== $pluginName) {
            throw PluginException::pluginNameMismatch($plugin, $pluginNamespace);
        }

        return $plugin;
    }
}
