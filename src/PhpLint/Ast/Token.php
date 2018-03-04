<?php
declare(strict_types=1);

namespace PhpLint\Ast;

class Token
{
    private $type;
    private $value;
    private $sourceRange;

    /**
     * @param $type
     * @param $value
     * @param $sourceRange
     */
    public function __construct($type, $value, $sourceRange)
    {
        $this->type = $type;
        $this->value = $value;
        $this->sourceRange = $sourceRange;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return mixed
     */
    public function getSourceRange()
    {
        return $this->sourceRange;
    }
}
