<?php
namespace PhpLint\Ast;

class SimpleAstNode implements AstNode
{
    /** @var string the type of the AST node as defined by NodeType */
    private $type;

    private $properties = [];

    public function __construct(string $type, array $properties = [])
    {
        $this->type = $type;
        $this->properties = $properties;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function get(string $key)
    {
        return isset($this->properties[$key]) ? $this->properties[$key] : null;
    }
}
