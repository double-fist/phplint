<?php
declare(strict_types=1);

namespace PhpLint\Rules;

use Exception;

class RuleException extends Exception
{
    /**
     * @param string $ruleName
     * @return RuleException
     */
    public static function ruleNotFound(string $ruleName): RuleException
    {
        throw new self(sprintf(
            'Rule with name "%s" could not be loaded, because it neither is provided by a loaded plugin nor is it part of the default rules.',
            $ruleName
        ));
    }
}
