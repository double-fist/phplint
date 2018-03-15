<?php
declare(strict_types=1);

namespace PhpLint\Test\Ast;

use PhpLint\Ast\AstNode;
use PhpLint\Configuration\ConfigurationValidator;

class TestAstNode implements AstNode
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var AstNode|null
     */
    private $parent = null;

    /**
     * @var AstNode[]
     */
    private $children;

    /**
     * @param string $rootId
     * @param array $children
     * @return TestAstNode
     */
    public static function createFromArrayDescription(string $rootId, array $children): TestAstNode
    {
        $childrenIds = (ConfigurationValidator::isAssocArray($children)) ? array_keys($children) : $children;
        $childrenDescriptions = (ConfigurationValidator::isAssocArray($children)) ? $children : array_fill_keys($childrenIds, []);

        $children = array_map(
            function (string $childId) use ($childrenDescriptions) {
                return self::createFromArrayDescription($childId, $childrenDescriptions[$childId]);
            },
            $childrenIds
        );

        return new self($rootId, $children);
    }

    /**
     * @param string $id
     * @param AstNode[] $children
     */
    public function __construct(string $id, array $children = [])
    {
        $this->id = $id;
        $this->children = $children;
        foreach ($this->children as $child) {
            $child->setParent($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function getType(): string
    {
        return __CLASS__;
    }

    /**
     * @inheritdoc
     */
    public function get(string $key)
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function setParent(AstNode $parent)
    {
        $this->parent = $parent;
    }

    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @inheritdoc
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param mixed $otherNode
     * @return bool
     */
    public function equals($otherNode): bool
    {
        return ($otherNode instanceof TestAstNode) && $otherNode->getId() === $this->id;
    }
}
