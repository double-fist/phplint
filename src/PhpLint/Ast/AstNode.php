<?php
declare(strict_types=1);

namespace PhpLint\Ast;

interface AstNode
{
    public function getType(): string;

    public function get(string $key);

    /**
     * @param AstNode $parent
     */
    public function setParent(AstNode $parent);

    /**
     * @return AstNode|null
     */
    public function getParent();

    public function getChildren(): array;
}
