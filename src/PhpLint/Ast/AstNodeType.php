<?php
declare(strict_types=1);

namespace PhpLint\Ast;

class AstNodeType
{
    const CLASS_CONST = 'CLASS_CONST';
    const CLASS_DECLARATION = 'CLASS';
    const CLASS_METHOD = 'CLASS_METHOD';
    const IDENTIFIER = 'NAME';
    const NAME = 'NAME';
    const NAMESPACE = 'NAMESPACE';
    const SOURCE_ROOT = 'SOURCE_ROOT';
}
