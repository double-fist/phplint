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

    /**
     * @param string $ruleName
     * @param string $givenNodeType
     * @param string[] $expectedNodeTypes
     * @return RuleException
     */
    public static function unexpectedNodeTypeInRule(
        string $ruleName,
        string $givenNodeType,
        array $expectedNodeTypes
    ): RuleException {
        throw new self(sprintf(
            'Rule "%s" encountered a node of unexpected type "%s". Expected one of the following types instead: "%s".',
            $ruleName,
            $givenNodeType,
            implode('", "', $expectedNodeTypes)
        ));
    }
}
