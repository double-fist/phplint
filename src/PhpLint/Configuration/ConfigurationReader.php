<?php
declare(strict_types=1);

namespace PhpLint\Configuration;

use FilesystemIterator;
use Symfony\Component\Yaml\Yaml;

class ConfigurationReader
{
    const VALID_CONFIG_FILE_NAMES = [
        '.phplintrc.json',
        '.phplintrc.php',
        '.phplintrc.yaml',
        '.phplintrc.yml',
    ];

    /**
     * @param string $fileName
     * @return bool
     */
    public function isValidConfigFileName(string $fileName): bool
    {
        return in_array($fileName, self::VALID_CONFIG_FILE_NAMES);
    }

    /**
     * @param string $directoryPath
     * @return array
     * @throws ConfigurationException
     */
    public function readDirectoryConfig(string $directoryPath): array
    {
        if (!is_dir($directoryPath)) {
            throw ConfigurationException::pathIsNoDirectory($directoryPath);
        }

        // Search the director for a configuration file
        $fileIterator = new FilesystemIterator($directoryPath);
        foreach ($fileIterator as $file) {
            if ($file->isFile() && self::isValidConfigFileName($file->getFilename())) {
                return $this->readFromPath($file->getPathname());
            }
        }

        return [];
    }

    /**
     * @param stirng $filePath
     * @return array
     * @throws ConfigurationException
     */
    public function readFromPath(string $filePath): array
    {
        if (!is_file($filePath)) {
            throw ConfigurationException::pathIsNoFile($filePath);
        }

        // Parse file
        $fileType = pathinfo($filePath)['extension'];
        switch ($fileType) {
            case 'json':
                // Read file contents
                $fileContents = file_get_contents($filePath);
                if ($fileContents === false) {
                    throw ConfigurationException::failedToReadFile($filePath);
                }

                return json_decode($fileContents, true);
            case 'php':
                return include $filePath;
            case 'yaml':
            case 'yml':
                return Yaml::parseFile($filePath);
            default:
                throw ConfigurationException::invalidFileType($filePath);
        }
    }
}
