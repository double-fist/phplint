<?php
declare(strict_types=1);

namespace PhpLint\Plugin;

/**
 * Implement this interface in a class named 'Config' in the namespace PhpLint\Plugin\<VENDOR>\<NAME>, e.g.:
 *
 *  namespace PhpLint\Plugin\Acme\MyPlugin;
 *
 *  class Config {}
 */
interface ConfigPlugin
{
    /**
     * The name must following the format 'phplint-config-<VENDOR>-<NAME>', e.g.:
     *
     *  phplint-config-acme-my-plugin
     *
     * @return string
     */
    public function getName(): string;

    /**
     * @return string[]
     */
    public function getExtendedConfigNames(): array;

    /**
     * @return array
     */
    public function getRuleConfigs(): array;
}
