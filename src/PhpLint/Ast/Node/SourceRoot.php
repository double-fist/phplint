<?php
declare(strict_types=1);

namespace PhpLint\Ast\Node;

use PhpParser\Node;
use PhpParser\NodeAbstract;

class SourceRoot extends NodeAbstract
{
    /**
     * @var Node[]
     */
    public $contents = [];

    /**
     * @param Node[] $contents
     * @param array $attributes
     */
    public function __construct(array $contents, array $attributes = [])
    {
        parent::__construct($attributes);
        $this->contents = $contents;
    }

    /**
     * @inhertidoc
     */
    public function getType(): string
    {
        return 'SourceRoot';
    }

    /**
     * @inhertidoc
     */
    public function getSubNodeNames(): array
    {
        return ['contents'];
    }
}
