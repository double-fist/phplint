<?php
declare(strict_types=1);

namespace PhpLint\Linter\Directive;

class DisableDirective extends Directive
{
    const TYPE_DISABLE = 'disable';
    const TYPE_DISABLE_LINE = 'disable-line';
    const TYPE_DISABLE_NEXT_LINE = 'disable-next-line';
    const TYPE_ENABLE = 'enable';

    /**
     * @return string|null
     */
    public function getRuleId()
    {
        return $this->getValue();
    }
}
