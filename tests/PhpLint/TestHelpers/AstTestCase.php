<?php
declare(strict_types=1);

namespace PhpLint\TestHelpers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Util\Blacklist;

class AstTestCase extends TestCase
{
    public static function assertNodeType($expectedType, $node)
    {
        self::assertThat($node, new AstNodeTypeConstraint($expectedType));
    }
}

Blacklist::$blacklistedClassNames['PhpLint\TestHelpers\AstTestCase'] = 1;
