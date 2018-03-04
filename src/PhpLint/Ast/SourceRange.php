<?php
declare(strict_types=1);

namespace PhpLint\Ast;

class SourceRange
{
    /** @var SourceLocation */
    private $start;

    /** @var SourceLocation */
    private $end;

    /**
     * @param SourceLocation $start
     * @param SourceLocation $end
     */
    private function __construct(SourceLocation $start, SourceLocation $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    /**
     * @return SourceLocation
     */
    public function getStart(): SourceLocation
    {
        return $this->start;
    }

    /**
     * @return SourceLocation
     */
    public function getEnd(): SourceLocation
    {
        return $this->end;
    }

    public function __toString()
    {
        return sprintf(
            '%s-%s',
            $this->start,
            $this->end
        );
    }

    public static function between(SourceLocation $start, SourceLocation $end)
    {
        if ($start->isGreaterThan($end)) {
            throw new \InvalidArgumentException(
                sprintf('Given start source location %s is after given end source location %s', $start, $end)
            );
        }

        return new self($start, $end);
    }

    public static function spanningRanges(SourceRange $range1, SourceRange $range2)
    {
        $start = $range1->getStart()->isSmallerThanOrEquals($range2->getStart()) ? $range1->getStart() : $range2->getStart();
        $end = $range1->getEnd()->isGreaterThanOrEquals($range2->getEnd()) ? $range1->getEnd() : $range2->getEnd();

        return self::between($start, $end);
    }
}
