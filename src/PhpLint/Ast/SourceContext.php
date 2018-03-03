<?php
declare(strict_types=1);

namespace PhpLint\Ast;

interface SourceContext
{
    /**
     * @return null|string
     */
    public function getPath(): string;

    /**
     * @return string
     */
    public function getContent(): string;

    /**
     * @return AstNode
     */
    public function getAst(): AstNode;

    public function getSourceRangeOfNode(AstNode $node): SourceRange;
}
