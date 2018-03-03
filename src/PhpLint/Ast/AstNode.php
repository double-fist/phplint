<?php
declare(strict_types=1);

namespace PhpLint\Ast;

interface AstNode
{
    public function getType(): string;

    public function get(string $key);

    public function getChildren();
}
