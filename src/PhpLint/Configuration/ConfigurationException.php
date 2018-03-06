<?php
declare(strict_types=1);

namespace PhpLint\Configuration;

use Exception;

class ConfigurationException extends Exception
{
    /**
     * @param string $path
     * @return ConfigurationException
     */
    public static function pathIsNoDirectory(string $path): ConfigurationException
    {
        return new self(sprintf(
            'The path "%s" is not a valid directory.',
            $path
        ));
    }

    /**
     * @param string $path
     * @return ConfigurationException
     */
    public static function pathIsNoFile(string $path): ConfigurationException
    {
        return new self(sprintf(
            'The path "%s" is not a valid file.',
            $path
        ));
    }

    /**
     * @param string $path
     * @return ConfigurationException
     */
    public static function failedToReadFile(string $path): ConfigurationException
    {
        return new self(sprintf(
            'Failed to read configuration file at path "%s".',
            $path
        ));
    }

    /**
     * @param string $path
     * @return ConfigurationException
     */
    public static function invalidFileType(string $path): ConfigurationException
    {
        $pathInfo = pathinfo($path);

        return new self(sprintf(
            'The configuration file at path "%s" has an invalid type (%s).',
            $path,
            (isset($pathInfo['extension'])) ? $pathInfo['extension'] : 'n/a'
        ));
    }

    /**
     * @param string $key
     * @return ConfigurationException
     */
    public static function invalidKey(string $key): ConfigurationException
    {
        return new self(sprintf(
            'The config key "%s" is invalid.',
            $key
        ));
    }
}
