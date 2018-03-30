<?php
declare(strict_types=1);

namespace PhpLint\Util;

use FilesystemIterator;

class IgnoreFileParser
{
    const IGNORE_FILENAME = '.phplintignore';

    /**
     * @param string $directoryPath
     * @return string[]
     * @throws ConfigurationException If either the given path is not a directory of the .phplintignore file could not be read.
     */
    public function readIgnorePatterns(string $directoryPath): array
    {
        if (!is_dir($directoryPath)) {
            throw ConfigurationException::pathIsNoDirectory($directoryPath);
        }

        // Check the path for a .phplintignore file
        $ignoreFilePath = rtrim($directoryPath, '/') . '/' . self::IGNORE_FILENAME;
        if (!is_file($ignoreFilePath)) {
            return [];
        }

        // Read the file contents
        $handle = fopen($ignoreFilePath, 'r');
        if (!$handle) {
            throw ConfigurationException::failedToReadFile($ignoreFilePath);
        }
        $ignorePatterns = [];
        $line = fgets($handle);
        while ($line !== false) {
            // Remove comments and empty lines
            $line = trim(preg_replace('/#.*/', '', $line));
            if (mb_strlen($line) > 0) {
                $ignorePatterns[] = $line;
            }
            $line = fgets($handle);
        }
        fclose($handle);

        return $ignorePatterns;
    }
}
