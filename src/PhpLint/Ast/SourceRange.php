<?php
declare(strict_types=1);

namespace PhpLint\Ast;

class SourceRange
{
    /** @var int */
    private $startLine;

    /** @var int */
    private $endLine;

    /** @var int */
    private $startColumn;

    /** @var int */
    private $endColumn;

    /**
     * @param int $startLine
     * @param int $endLine
     * @param int $startColumn
     * @param int $endColumn
     */
    public function __construct(int $startLine, int $endLine, int $startColumn, int $endColumn)
    {
        $this->startLine = $startLine;
        $this->endLine = $endLine;
        $this->startColumn = $startColumn;
        $this->endColumn = $endColumn;
    }

    /**
     * @return int
     */
    public function getStartLine(): int
    {
        return $this->startLine;
    }

    /**
     * @return int
     */
    public function getEndLine(): int
    {
        return $this->endLine;
    }

    /**
     * @return int
     */
    public function getStartColumn(): int
    {
        return $this->startColumn;
    }

    /**
     * @return int
     */
    public function getEndColumn(): int
    {
        return $this->endColumn;
    }

    public function __toString()
    {
        return sprintf('%d:%d-%d:%d', $this->startLine, $this->startColumn, $this->endLine, $this->endColumn);
    }
}
