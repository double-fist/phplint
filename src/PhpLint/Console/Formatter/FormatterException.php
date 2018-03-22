<?php
declare(strict_types=1);

namespace PhpLint\Console\Formatter;

use Exception;

class FormatterException extends Exception
{
    /**
     * @param string $formatterName
     * @return FormatterException
     */
    public static function formatterNotFound(string $formatterName): FormatterException
    {
        return new self(sprintf(
            'Formatter with name "%s" does not exist.',
            $formatterName
        ));
    }
}
