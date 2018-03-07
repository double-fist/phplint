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
interface ConfigPlugin extends Plugin
{
    /**
     * @return string[]
     */
    public function getExtends(): array;

    /**
     * @return string[]
     */
    public function getPlugins(): array;

    /**
     * @return array
     */
    public function getRules(): array;
}
