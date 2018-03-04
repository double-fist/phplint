<?php
declare(strict_types=1);

namespace PhpLint\Util;

trait ComparableFromGreaterThan
{
    public abstract function isGreaterThan(Comparable $other);

    public function isGreaterThanOrEquals(Comparable $other)
    {
        return !$other->isGreaterThan($this);
    }

    public function isSmallerThan(Comparable $other)
    {
        return $other->isGreaterThan($this);
    }

    public function isSmallerThanOrEquals(Comparable $other)
    {
        return !$this->isGreaterThan($other);
    }

    public function isEqualTo(Comparable $other)
    {
        return !$this->isGreaterThan($other) && !$other->isGreaterThan($this);
    }
}
