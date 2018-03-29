<?php
declare(strict_types=1);

namespace PhpLint\Linter\Directive;

use PhpLint\Ast\SourceLocation;

class Directive
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var SourceLocation
     */
    private $sourceLocation;

    /**
     * @var string|null
     */
    private $value;

    /**
     * @param string $type
     * @param SourceLocation $sourceLocation
     * @param string|null $value
     */
    public function __construct(string $type, SourceLocation $sourceLocation, string $value = null)
    {
        $this->type = $type;
        $this->sourceLocation = $sourceLocation;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return SourceLocation
     */
    public function getSourceLocation(): SourceLocation
    {
        return $this->sourceLocation;
    }

    /**
     * @return string|null
     */
    public function getValue()
    {
        return $this->value;
    }
}
