<?php
declare(strict_types=1);

namespace PhpLint\Plugin;

/**
 * Extends this class in a class named 'Config' in the namespace PhpLint\Plugin\<VENDOR>\<NAME>, e.g.:
 *
 *  namespace PhpLint\Plugin\Acme\MyPlugin;
 *
 *  class Config extends PhpLint\Plugin\AbstractConfigPlugin {}
 */
abstract class AbstractConfigPlugin implements ConfigPlugin
{
    /**
     * @inheritdoc
     */
    abstract public function getName(): string;

    /**
     * @inheritdoc
     */
    abstract public function getExtends(): array;

    /**
     * @inheritdoc
     */
    abstract public function getPlugins(): array;

    /**
     * @inheritdoc
     */
    abstract public function getRules(): array;

    /**
     * @inheritdoc
     */
    public function getDependencies(): array
    {
        return array_unique(array_merge(
            $this->getExtends(),
            $this->getPlugins()
        ));
    }

    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        return [
            'extends' => $this->getExtends(),
            'plugins' => $this->getPlugins(),
            'rules' => $this->getRules(),
        ];
    }
}
