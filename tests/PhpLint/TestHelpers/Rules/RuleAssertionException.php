<?php
declare(strict_types=1);

namespace PhpLint\TestHelpers\Rules;

use Exception;

class RuleAssertionException extends Exception
{
    /**
     * @param string $message
     * @param Exception $previousException
     */
    public function __construct(string $message, Exception $previousException)
    {
        parent::__construct($message, 0, $previousException);
    }

    /**
     * @param RuleAcceptanceAssertion $ruleAssertion
     * @param Exception $previousException
     * @return RuleAssertionException
     */
    public static function acceptanceTestFailed(RuleAcceptanceAssertion $ruleAssertion, Exception $previousException): RuleAssertionException
    {
        $message = sprintf(
            "An exception was thrown while asserting that rule \"%s\" accepts code:\n\n%s",
            $ruleAssertion->getRule()->getDescription()->getIdentifier(),
            $ruleAssertion->getTestCode()
        );

        return new self($message, $previousException);
    }

    /**
     * @param RuleFixAssertion $ruleAssertion
     * @param Exception $previousException
     * @return RuleAssertionException
     */
    public static function fixTestFailed(RuleFixAssertion $ruleAssertion, Exception $previousException): RuleAssertionException
    {
        $message = sprintf(
            "An exception was thrown while asserting that rule \"%s\" fixes code:\n\n%s",
            $ruleAssertion->getRule()->getDescription()->getIdentifier(),
            $ruleAssertion->getTestCode()
        );

        return new self($message, $previousException);
    }

    /**
     * @param RuleRejectionAssertion $ruleAssertion
     * @param Exception $previousException
     * @return RuleAssertionException
     */
    public static function rejectionTestFailed(RuleRejectionAssertion $ruleAssertion, Exception $previousException): RuleAssertionException
    {
        $message = sprintf(
            "An exception was thrown while asserting that rule \"%s\" rejects code:\n\n%s",
            $ruleAssertion->getRule()->getDescription()->getIdentifier(),
            $ruleAssertion->getTestCode()
        );

        return new self($message, $previousException);
    }
}
