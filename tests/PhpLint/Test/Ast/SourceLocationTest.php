<?php
declare(strict_types=1);

namespace PhpLint\Test\Ast;

use PhpLint\Ast\SourceLocation;
use PHPUnit\Framework\TestCase;

class SourceLocationTest extends TestCase
{
    public function testSourceLocationLineGetter()
    {
        $sourceLocation = SourceLocation::atLineAndColumn(1, 2);

        $this->assertEquals(1, $sourceLocation->getLine());
    }

    public function testSourceLocationColumnGetter()
    {
        $sourceLocation = SourceLocation::atLineAndColumn(1, 2);

        $this->assertEquals(2, $sourceLocation->getColumn());
    }

    public function testConvertSourceLocationToString()
    {
        $sourceLocation = SourceLocation::atLineAndColumn(11, 43);

        $this->assertEquals('11:43', $sourceLocation);
    }

    public function testEqualSourceLocationEquality()
    {
        $sourceLocation1 = SourceLocation::atLineAndColumn(12, 1);
        $sourceLocation2 = SourceLocation::atLineAndColumn(12, 1);

        $this->assertTrue($sourceLocation1->isEqualTo($sourceLocation2));
    }

    public function testEqualSourceLocationInequalityLine()
    {
        $sourceLocation1 = SourceLocation::atLineAndColumn(12, 1);
        $sourceLocation2 = SourceLocation::atLineAndColumn(13, 1);

        $this->assertFalse($sourceLocation1->isEqualTo($sourceLocation2));
    }

    public function testEqualSourceLocationInequalityColumn()
    {
        $sourceLocation1 = SourceLocation::atLineAndColumn(12, 5);
        $sourceLocation2 = SourceLocation::atLineAndColumn(12, 1);

        $this->assertFalse($sourceLocation1->isEqualTo($sourceLocation2));
    }

    public function testSourceLocationIsSmallerThanOnTheSameLine()
    {
        $sourceLocation1 = SourceLocation::atLineAndColumn(12, 1);
        $sourceLocation2 = SourceLocation::atLineAndColumn(12, 5);

        $this->assertTrue($sourceLocation1->isSmallerThan($sourceLocation2));
    }

    public function testSourceLocationIsSmallerThanOnDifferentLines()
    {
        $sourceLocation1 = SourceLocation::atLineAndColumn(9, 1);
        $sourceLocation2 = SourceLocation::atLineAndColumn(12, 1);

        $this->assertTrue($sourceLocation1->isSmallerThan($sourceLocation2));
    }

    public function testSourceLocationIsGreaterThanOnTheSameLine()
    {
        $sourceLocation1 = SourceLocation::atLineAndColumn(9, 38);
        $sourceLocation2 = SourceLocation::atLineAndColumn(9, 15);

        $this->assertTrue($sourceLocation1->isGreaterThan($sourceLocation2));
    }

    public function testSourceLocationIsGreaterThanOnDifferentLines()
    {
        $sourceLocation1 = SourceLocation::atLineAndColumn(12, 1);
        $sourceLocation2 = SourceLocation::atLineAndColumn(9, 1);

        $this->assertTrue($sourceLocation1->isGreaterThan($sourceLocation2));
    }
}
