<?php
declare(strict_types=1);

namespace PhpLint\Linter;

use Exception;

class LintException extends Exception
{
    /**
     * @param string $path
     * @return LintException
     */
    public static function invalidPath(string $path): LintException
    {
        return new self(sprintf(
            'The path "%s" is not a valid PHP file.',
            $path
        ));
    }

    /**
     * @param string $path
     * @return LintException
     */
    public static function failedToReadFile(string $path): LintException
    {
        return new self(sprintf(
            'Failed to read PHP file at path "%s".',
            $path
        ));
    }

    /**
     * @param string $ruleId
     * @param mixed $severity
     * @return LintException
     */
    public static function invalidRuleSeverityReported(string $ruleId, $severity): LintException
    {
        return new self(sprintf(
            'The violation reported by rule "%s" used an invalid severity "%s".',
            $ruleId,
            $severity
        ));
    }
}
