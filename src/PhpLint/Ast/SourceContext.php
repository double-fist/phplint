<?php
declare(strict_types=1);

namespace PhpLint\Ast;

use PhpLint\Ast\Node\SourceRoot;
use PhpParser\Node;

interface SourceContext
{
    /**
     * @return string|null
     */
    public function getPath();

    /**
     * @return string
     */
    public function getContent(): string;

    /**
     * @return SourceRoot
     */
    public function getAst(): SourceRoot;

    /**
     * @return array
     */
    public function getTokens(): array;

    /**
     * @param Node $node
     * @return SourceRange
     */
    public function getSourceRangeOfNode(Node $node): SourceRange;
}
