<?php
namespace PhpLint\Ast;

interface AstNode
{
    public function getType(): string;

    public function get(string $key);
}
