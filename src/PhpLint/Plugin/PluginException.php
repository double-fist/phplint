<?php
declare(strict_types=1);

namespace PhpLint\Plugin;

use Exception;

class PluginException extends Exception
{
    /**
     * @param string $pluginName
     * @param array $circularDependencies
     * @return PluginException
     */
    public static function invalidPluginType(string $pluginType): PluginException
    {
        throw new self(sprintf(
            'The plugin type "%s" is invalid.',
            $pluginType
        ));
    }

    /**
     * @param string $pluginName
     * @param array $circularDependencies
     * @return PluginException
     */
    public static function circularDependecies(string $pluginName, array $circularDependencies): PluginException
    {
        throw new self(sprintf(
            'Cannot load plugin "%s" because it has circular dependencies on the following plugins: "%s"',
            $pluginName,
            implode('", "', $circularDependencies)
        ));
    }

    /**
     * @param string $pluginName
     * @param string $pluginNamespace
     * @return PluginException
     */
    public static function pluginNamespaceNotFound(string $pluginName, string $pluginNamespace): PluginException
    {
        return new self(sprintf(
            'The plugin named "%s" could not be loaded, because its namespace "%s" does not exist.',
            $pluginName,
            $pluginNamespace
        ));
    }

    /**
     * @param Plugin $plugin
     * @param string $pluginNamespace
     * @return PluginException
     */
    public static function pluginNameMismatch(Plugin $plugin, string $pluginNamespace): PluginException
    {
        return new self(sprintf(
            'The plugin\'s name "%s" does not match its namespace "%s".',
            $plugin->getName(),
            $pluginNamespace
        ));
    }
}
