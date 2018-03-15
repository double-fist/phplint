<?php
declare(strict_types=1);

namespace PhpLint\Ast;

interface AstNode
{
    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @param strign $key
     * @return mixed
     */
    public function get(string $key);

    /**
     * @param AstNode $parent
     */
    public function setParent(AstNode $parent);

    /**
     * @return AstNode|null
     */
    public function getParent();

    /**
     * @return AstNode[]
     */
    public function getChildren(): array;
}
