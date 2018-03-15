<?php
declare(strict_types=1);

namespace PhpLint\Ast;

use Iterator;

class AstNodeTraverser implements Iterator
{
    /**
     * @var AstNode
     */
    private $rootNode;

    /**
     * @var AstNode
     */
    private $currentNode;

    /**
     * @var int
     */
    private $counter = 0;

    /**
     * @param AstNode $rootNode
     */
    public function __construct(AstNode $rootNode)
    {
        $this->rootNode = $rootNode;
        $this->currentNode = $rootNode;
    }

    /**
     * @inheritdoc
     */
    public function current()
    {
        return $this->currentNode;
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return $this->counter;
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        $this->currentNode = self::findNextNode($this->currentNode);
        $this->counter += 1;
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        $this->currentNode = $this->rootNode;
        $this->counter = 0;
    }

    /**
     * @inheritdoc
     */
    public function valid(): bool
    {
        return $this->currentNode !== null;
    }

    /**
     * @param AstNode $node
     * @return AstNode|null
     */
    protected static function findNextNode(AstNode $node, $isTraversingDown = true)
    {
        // Check for children first (depth first), if we're traversing the tree downwards
        if ($isTraversingDown && count($node->getChildren()) > 0) {
            return $node->getChildren()[0];
        }

        if (!$node->getParent()) {
            // No nodes left
            return null;
        }

        // Check for siblings
        $siblings = $node->getParent()->getChildren();
        $childIndex = array_search($node, $siblings);
        $nextSiblingIndex = $childIndex + 1;
        if ($nextSiblingIndex < count($siblings)) {
            return $siblings[$nextSiblingIndex];
        }

        // Current node is last or only sibling, hence traverse the tree up and keep searching
        return self::findNextNode($node->getParent(), false);
    }
}
