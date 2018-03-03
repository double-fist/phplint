<?php
namespace PhpLint\TestHelpers;

use PhpLint\Ast\AstNode;
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
        if (!($other instanceof AstNode)) {
            return false;
        }

        return $other->getType() === $this->expectedNodeType;
    }

    public function toString()
    {
        return sprintf(
            'is an AST node of type %s',
            $this->expectedNodeType
        );
    }
}
