<?php
declare(strict_types=1);

namespace PhpLint\Ast;

use Iterator;
use PhpParser\Node;

class AstNodeTraverser implements Iterator
{
    const PARENT_ATTRIBUTE_NAME = 'phplint_parent';

    /**
     * @var Node
     */
    private $rootNode;

    /**
     * @var Node
     */
    private $currentNode;

    /**
     * @var int
     */
    private $counter = 0;

    /**
     * @param Node $node
     * @return Node|null
     */
    public static function getParent(Node $node)
    {
        return $node->getAttribute(self::PARENT_ATTRIBUTE_NAME);
    }

    /**
     * @param Node $node
     * @return Node[]
     */
    public static function getChildren(Node $node): array
    {
        $children = [];
        foreach ($node->getSubNodeNames() as $subNodeName) {
            $subNode = $node->$subNodeName;
            if (is_array($subNode)) {
                // Keep only real node children
                $children = array_merge($children, array_values(array_filter(
                    $subNode,
                    function ($node) {
                        return $node instanceof Node;
                    }
                )));
            } elseif ($subNode instanceof Node) {
                $children[] = $subNode;
            }
        }

        return $children;
    }

    /**
     * @param Node $node
     * @param bool $includeGivenNode
     * @return Node[]
     */
    public static function getSiblings(Node $node, bool $includeGivenNode = false): array
    {
        $parent = self::getParent($node);
        if (!$parent) {
            return [];
        }

        $siblings = self::getChildren($parent);
        if ($includeGivenNode) {
            return $siblings;
        }

        return array_values(array_filter(
            $siblings,
            function (Node $sibling) use ($node) {
                return $sibling !== $node;
            }
        ));
    }

    /**
     * @param Node $node
     */
    public static function createParentBackLinks(Node $node)
    {
        foreach (self::getChildren($node) as $child) {
            if ($child instanceof Node) {
                $child->setAttribute(self::PARENT_ATTRIBUTE_NAME, $node);
                self::createParentBackLinks($child);
            }
        }
    }

    /**
     * @param Node $rootNode
     */
    public function __construct(Node $rootNode)
    {
        self::createParentBackLinks($rootNode);
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
        $this->currentNode = $this->findNextNode($this->currentNode);
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
     * @param Node $node
     * @return Node|null
     */
    protected function findNextNode(Node $node, $isTraversingDown = true)
    {
        // Check for children first (depth first), if we're traversing the tree downwards
        if ($isTraversingDown && count(self::getChildren($node)) > 0) {
            return self::getChildren($node)[0];
        }

        $parent = self::getParent($node);
        if (!$parent || $node === $this->rootNode) {
            // No nodes left
            return null;
        }

        // Check for siblings
        $siblings = self::getSiblings($node, true);
        $childIndex = array_search($node, $siblings);
        $nextSiblingIndex = $childIndex + 1;
        if ($nextSiblingIndex < count($siblings)) {
            return $siblings[$nextSiblingIndex];
        }

        // Current node is last or only sibling, hence traverse the tree up and keep searching
        return $this->findNextNode($parent, false);
    }
}
