<?php
namespace PhpLint;

use PhpLint\Ast\SourceContext;
use PhpLint\PhpParser\PhpParser;

abstract class AbstractParser
{
    abstract public function parse(string $code, string $path): SourceContext;

    public static function create(): self
    {
        return new PhpParser();
    }
}
