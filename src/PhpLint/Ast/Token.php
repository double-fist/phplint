<?php
declare(strict_types=1);

namespace PhpLint\Ast;

class Token
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $value;

    /**
     * @var SourceRange
     */
    private $sourceRange;

    /**
     * @param string $type
     * @param string $value
     * @param SourceRange $sourceRange
     */
    public function __construct(string $type, string $value, SourceRange $sourceRange)
    {
        $this->type = $type;
        $this->value = $value;
        $this->sourceRange = $sourceRange;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return SourceRange
     */
    public function getSourceRange(): SourceRange
    {
        return $this->sourceRange;
    }
}
