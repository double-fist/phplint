<?php
namespace PhpLint;

use PhpLint\Ast\AstNode;
use PhpLint\PhpParser\PhpParser;

abstract class AbstractParser
{
    abstract public function parse(string $path, string $code): AstNode;

    public static function create(): self
    {
        return new PhpParser();
    }
}
