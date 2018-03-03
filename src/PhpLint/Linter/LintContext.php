<?php
declare(strict_types=1);

namespace PhpLint\Linter;

class LintContext
{
    /**
     * @var string|null
     */
    protected $filePath = null;

    /**
     * @param string|null $filePath
     */
    public function __construct(string $filePath = null)
    {
        $this->filePath = $filePath;
    }

    /**
     * @return string|null
     */
    public function getFilePath()
    {
        return $this->filePath;
    }
}
