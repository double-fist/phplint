<?php
declare(strict_types=1);

namespace PhpLint\TestHelpers;

use PhpParser\Node;
use PHPUnit\Framework\Constraint\Constraint;

class AstNodeTypeConstraint extends Constraint
{
    private $expectedNodeType;

    public function __construct(string $expectedNodeType)
    {
        parent::__construct();

        $this->expectedNodeType = $expectedNodeType;
    }

    public function matches($other)
    {
        if (!($other instanceof Node)) {
            return false;
        }

        return get_class($other) === $this->expectedNodeType;
    }

    public function toString()
    {
        return sprintf(
            'is an AST node of type %s',
            $this->expectedNodeType
        );
    }
}
