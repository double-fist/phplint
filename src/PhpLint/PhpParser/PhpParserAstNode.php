<?php
declare(strict_types=1);

namespace PhpLint\PhpParser;

use PhpLint\Ast\AstNode;
use PhpParser\Node;

class PhpParserAstNode implements AstNode
{
    /** @var string the type of the AST node as defined by NodeType */
    private $type;

    /** @var array */
    private $properties = [];

    /** @var Node|null */
    private $wrappedNode = null;

    public function __construct(string $type, array $properties = [], Node $wrappedNode = null)
    {
        $this->type = $type;
        $this->properties = $properties;
        $this->wrappedNode = $wrappedNode;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function get(string $key)
    {
        return isset($this->properties[$key]) ? $this->properties[$key] : null;
    }

    public function getWrappedNode()
    {
        return $this->wrappedNode;
    }

    public function getChildren()
    {
        $children = [];

        $propertyNames = array_keys($this->properties);
        sort($propertyNames);

        foreach ($propertyNames as $propertyName) {
            $propertyValue = $this->properties[$propertyName];

            if ($propertyValue instanceof AstNode) {
                $children[] = $propertyValue;
            } elseif (is_array($propertyValue)) {
                $nestedNodes = array_values(array_filter($propertyValue, function ($element) {
                    return $element instanceof AstNode;
                }));
                $children = array_merge($children, $nestedNodes);
            }
        }

        return $children;
    }
}
