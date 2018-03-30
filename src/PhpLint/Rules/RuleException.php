<?php
declare(strict_types=1);

namespace PhpLint\Rules;

use Exception;

class RuleException extends Exception
{
    /**
     * @param string $ruleId
     * @return RuleException
     */
    public static function ruleNotFound(string $ruleId): RuleException
    {
        throw new self(sprintf(
            'Rule with name "%s" could not be loaded, because it neither is provided by a loaded plugin nor is it part of the default rules.',
            $ruleId
        ));
    }

    /**
     * @param string $ruleId
     * @param string $givenNodeType
     * @param string[] $expectedNodeTypes
     * @return RuleException
     */
    public static function unexpectedNodeTypeInRule(
        string $ruleId,
        string $givenNodeType,
        array $expectedNodeTypes
    ): RuleException {
        throw new self(sprintf(
            'Rule "%s" encountered a node of unexpected type "%s". Expected one of the following types instead: "%s".',
            $ruleId,
            $givenNodeType,
            implode('", "', $expectedNodeTypes)
        ));
    }
}
