<?php
declare(strict_types=1);

namespace PhpLint\Test\Ast;

use PhpLint\Ast\SourceLocation;
use PhpLint\Ast\SourceRange;
use PHPUnit\Framework\TestCase;

class SourceRangeTest extends TestCase
{
    public function testSourceRangeStartGetter()
    {
        $sourceRange = SourceRange::between(
            SourceLocation::atLineAndColumn(1, 1),
            SourceLocation::atLineAndColumn(1, 37)
        );

        $this->assertEquals(SourceLocation::atLineAndColumn(1, 1), $sourceRange->getStart());
    }

    public function testSourceRangeEndGetter()
    {
        $sourceRange = SourceRange::between(
            SourceLocation::atLineAndColumn(1, 1),
            SourceLocation::atLineAndColumn(1, 37)
        );

        $this->assertEquals(SourceLocation::atLineAndColumn(1, 37), $sourceRange->getEnd());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Given start source location 1:37 is after given end source location 1:1
     */
    public function testTryingToCreateASourceRangeWithAStartLocationAfterTheEndLocationThrows()
    {
        SourceRange::between(
            SourceLocation::atLineAndColumn(1, 37),
            SourceLocation::atLineAndColumn(1, 1)
        );
    }

    public function testSourceRangeSpanningTwoOverlappingSourceRangesOnTheSameLine()
    {
        $sourceRange1 = SourceRange::between(
            SourceLocation::atLineAndColumn(4, 5),
            SourceLocation::atLineAndColumn(4, 37)
        );
        $sourceRange2 = SourceRange::between(
            SourceLocation::atLineAndColumn(4, 23),
            SourceLocation::atLineAndColumn(4, 76)
        );
        $combinedSourceRange = SourceRange::spanningRanges($sourceRange1, $sourceRange2);

        $this->assertEquals(
            SourceRange::between(
                SourceLocation::atLineAndColumn(4, 5),
                SourceLocation::atLineAndColumn(4, 76)
            ),
            $combinedSourceRange
        );
    }

    public function testSourceRangeSpanningTwoOverlappingSourceRangesOnTheSameLineReverseOrder()
    {
        $sourceRange1 = SourceRange::between(
            SourceLocation::atLineAndColumn(4, 23),
            SourceLocation::atLineAndColumn(4, 76)
        );
        $sourceRange2 = SourceRange::between(
            SourceLocation::atLineAndColumn(4, 5),
            SourceLocation::atLineAndColumn(4, 37)
        );
        $combinedSourceRange = SourceRange::spanningRanges($sourceRange1, $sourceRange2);

        $this->assertEquals(
            SourceRange::between(
                SourceLocation::atLineAndColumn(4, 5),
                SourceLocation::atLineAndColumn(4, 76)
            ),
            $combinedSourceRange
        );
    }

    public function testSourceRangeSpanningTwoDisjointSourceRangesOnTheSameLine()
    {
        $sourceRange1 = SourceRange::between(
            SourceLocation::atLineAndColumn(4, 2),
            SourceLocation::atLineAndColumn(4, 14)
        );
        $sourceRange2 = SourceRange::between(
            SourceLocation::atLineAndColumn(4, 18),
            SourceLocation::atLineAndColumn(4, 23)
        );
        $combinedSourceRange = SourceRange::spanningRanges($sourceRange1, $sourceRange2);

        $this->assertEquals(
            SourceRange::between(
                SourceLocation::atLineAndColumn(4, 2),
                SourceLocation::atLineAndColumn(4, 23)
            ),
            $combinedSourceRange
        );
    }

    public function testSourceRangeSpanningTwoDisjointSourceRangesOnTheSameLineReverseOrder()
    {
        $sourceRange1 = SourceRange::between(
            SourceLocation::atLineAndColumn(4, 18),
            SourceLocation::atLineAndColumn(4, 23)
        );
        $sourceRange2 = SourceRange::between(
            SourceLocation::atLineAndColumn(4, 2),
            SourceLocation::atLineAndColumn(4, 14)
        );
        $combinedSourceRange = SourceRange::spanningRanges($sourceRange1, $sourceRange2);

        $this->assertEquals(
            SourceRange::between(
                SourceLocation::atLineAndColumn(4, 2),
                SourceLocation::atLineAndColumn(4, 23)
            ),
            $combinedSourceRange
        );
    }

    public function testSourceRangeSpanningTwoOverlappingSourceRangesOnDifferentLines()
    {
        $sourceRange1 = SourceRange::between(
            SourceLocation::atLineAndColumn(4, 5),
            SourceLocation::atLineAndColumn(29, 2)
        );
        $sourceRange2 = SourceRange::between(
            SourceLocation::atLineAndColumn(6, 2),
            SourceLocation::atLineAndColumn(32, 76)
        );
        $combinedSourceRange = SourceRange::spanningRanges($sourceRange1, $sourceRange2);

        $this->assertEquals(
            SourceRange::between(
                SourceLocation::atLineAndColumn(4, 5),
                SourceLocation::atLineAndColumn(32, 76)
            ),
            $combinedSourceRange
        );
    }

    public function testSourceRangeSpanningTwoOverlappingSourceRangesOnDifferentLinesReverseOrder()
    {
        $sourceRange1 = SourceRange::between(
            SourceLocation::atLineAndColumn(6, 2),
            SourceLocation::atLineAndColumn(32, 76)
        );
        $sourceRange2 = SourceRange::between(
            SourceLocation::atLineAndColumn(4, 5),
            SourceLocation::atLineAndColumn(29, 2)
        );
        $combinedSourceRange = SourceRange::spanningRanges($sourceRange1, $sourceRange2);

        $this->assertEquals(
            SourceRange::between(
                SourceLocation::atLineAndColumn(4, 5),
                SourceLocation::atLineAndColumn(32, 76)
            ),
            $combinedSourceRange
        );
    }
}
