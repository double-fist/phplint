<?php
declare(strict_types=1);

namespace PhpLint\Ast;

use PhpLint\Util\Comparable;
use PhpLint\Util\ComparableFromGreaterThan;

class SourceLocation implements Comparable
{
    use ComparableFromGreaterThan;

    /** @var int */
    private $line;

    /** @var int */
    private $column;

    /**
     * @param int $line
     * @param int $column
     */
    private function __construct(int $line, int $column)
    {
        $this->line = $line;
        $this->column = $column;
    }

    /**
     * @return int
     */
    public function getLine(): int
    {
        return $this->line;
    }

    /**
     * @return int
     */
    public function getColumn(): int
    {
        return $this->column;
    }

    public function __toString()
    {
        return sprintf(
            '%d:%d',
            $this->line,
            $this->column
        );
    }

    public function isGreaterThan(Comparable $other)
    {
        if (!($other instanceof SourceLocation)) {
            throw new \InvalidArgumentException(sprintf(
                'isGreaterThan expected an object of type %s, got an object of type %s instead.',
                SourceLocation::class,
                get_class($other)
            ));
        }

        return ($this->line > $other->line) || (($this->line === $other->line) && ($this->column > $other->column));
    }

    public static function atLineAndColumn(int $line, int $column): self
    {
        return new self($line, $column);
    }
}
