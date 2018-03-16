<?php
declare(strict_types=1);

namespace PhpLint\Test\Ast;

use PhpLint\Ast\NodeTraverser;
use PhpLint\Configuration\ConfigurationValidator;
use PhpParser\Node;
use PhpParser\NodeAbstract;

class TestNode extends NodeAbstract
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var Node[]
     */
    public $children;

    /**
     * @param string $rootId
     * @param array $children
     * @return TestNode
     */
    public static function createFromArrayDescription(string $rootId, array $children): TestNode
    {
        $childrenIds = (ConfigurationValidator::isAssocArray($children)) ? array_keys($children) : $children;
        $childrenDescriptions = (ConfigurationValidator::isAssocArray($children)) ? $children : array_fill_keys($childrenIds, []);

        $children = array_map(
            function (string $childId) use ($childrenDescriptions) {
                return self::createFromArrayDescription($childId, $childrenDescriptions[$childId]);
            },
            $childrenIds
        );

        $root = new self($rootId, $children);
        NodeTraverser::createParentBackLinks($root);

        return $root;
    }

    /**
     * @param string $id
     * @param Node[] $children
     */
    public function __construct(string $id, array $children = [])
    {
        parent::__construct([]);
        $this->id = $id;
        $this->children = $children;
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
    public function getSubNodeNames(): array
    {
        return ['children'];
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
        return ($otherNode instanceof TestNode) && $otherNode->getId() === $this->id;
    }
}
