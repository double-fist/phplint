<?php
declare(strict_types=1);

namespace PhpLint\Ast;

class AstNodeType
{
    const SOURCE_ROOT = 'SOURCE_ROOT';
    const NAMESPACE = 'NAMESPACE';
    const NAME = 'NAME';
    const CLASS_DECLARATION = 'CLASS';
    const CLASS_METHOD = 'CLASS_METHOD';
    const IDENTIFIER = 'NAME';
}
