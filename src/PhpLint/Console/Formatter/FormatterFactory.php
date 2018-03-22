<?php
declare(strict_types=1);

namespace PhpLint\Console\Formatter;

class FormatterFactory
{
    /**
     * @param string $formatterName
     * @return LintResultFormatter
     * @throws FormatterException if the formatter with the given $formatterName does not exist.
     */
    public function createLintResultFormatter(string $formatterName): LintResultFormatter
    {
        switch ($formatterName) {
            case StylishLintResultFormatter::NAME:
                return new StylishLintResultFormatter();
            default:
                throw FormatterException::formatterNotFound($formatterName);
        }
    }
}
