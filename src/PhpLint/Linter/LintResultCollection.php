<?php
declare(strict_types=1);

namespace PhpLint\Linter;

use Countable;
use OutOfBoundsException;

class LintResultCollection implements Countable
{
    /**
     * @var array
     */
    protected $results = [];

    /**
     * @inheritdoc
     */
    public function count()
    {
        return count($this->getAllViolations());
    }

    /**
     * @return bool
     */
    public function containsErrors(): bool
    {
        foreach ($this->results as $lintResult) {
            if ($lintResult->containsErrors()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $filePath
     * @return LintResult
     * @throws OutOfBoundsException if no result exists for the given $filePath
     */
    public function getResult(string $filePath): LintResult
    {
        if (isset($this->results[$filePath])) {
            return $this->results[$filePath];
        }

        throw new OutOfBoundsException(sprintf(
            'No result found for given file path "%s"',
            $filePath
        ));
    }

    /**
     * @param string $filePath
     * @param LintResult $lintResult
     */
    public function addResult(string $filePath, LintResult $lintResult)
    {
        $this->results[$filePath] = $lintResult;
    }

    /**
     * @return string[]
     */
    public function getFilePaths(): array
    {
        return array_keys($this->results);
    }

    /**
     * @return RuleViolation[]
     */
    public function getAllViolations(): array
    {
        return array_merge(...array_map(
            function (LintResult $lintResult) {
                return $lintResult->getViolations();
            },
            array_values($this->results)
        ));
    }
}
