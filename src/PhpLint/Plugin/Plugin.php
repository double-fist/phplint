<?php
declare(strict_types=1);

namespace PhpLint\Plugin;

interface Plugin
{
    /**
     * The name must following the format '<VENDOR>/<NAME>' and correspond to the chosen namespace, e.g.:
     *
     *  Acme/MyPlugin
     *
     * @return string
     */
    public function getName(): string;

    /**
     * @return string[]
     */
    public function getDependencies(): array;

    /**
     * @return array
     */
    public function toArray(): array;
}
