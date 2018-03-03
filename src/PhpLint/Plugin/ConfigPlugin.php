<?php
declare(strict_types=1);

namespace PhpLint\Plugin;

/**
 * Implement this interface in a class named 'Config' in the namespace PhpLint\Plugin\<VENDOR>\<NAME>, e.g.:
 *
 *  namespace PhpLint\Plugin\Acme\MyConfig;
 *
 *  class Config implements PhpLint\Plugin\ConfigPlugin {}
 */
interface ConfigPlugin
{
    /**
     * The name must following the format '<VENDOR>/<NAME>' and correspond to the chosen namespace, e.g.:
     *
     *  Acme/MyConfig
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
