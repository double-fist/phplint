<?php
declare(strict_types=1);

namespace PhpLint\Util;

interface Comparable
{
    public function compare(Comparable $other): int;

    public function isGreaterThan(Comparable $other);

    public function isGreaterThanOrEquals(Comparable $other);

    public function isSmallerThan(Comparable $other);

    public function isSmallerThanOrEquals(Comparable $other);

    public function isEqualTo(Comparable $other);
}
