<?php
declare(strict_types=1);

namespace PhpLint\Ast;

class AstNodeType
{
    const ASSIGNMENT = 'ASSIGNMENT';
    const CLASS_CONST = 'CLASS_CONST';
    const CLASS_DECLARATION = 'CLASS_DECLARATION';
    const CLASS_METHOD = 'CLASS_METHOD';
    const EXPRESSION = 'EXPRESSION';
    const IDENTIFIER = 'IDENTIFIER';
    const NAME = 'NAME';
    const NAMESPACE = 'NAMESPACE';
    const SOURCE_ROOT = 'SOURCE_ROOT';
    const STRING = 'STRING';
    const VARIABLE = 'VARIABLE';
}
