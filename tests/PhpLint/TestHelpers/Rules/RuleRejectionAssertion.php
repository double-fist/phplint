<?php
declare(strict_types=1);

namespace PhpLint\TestHelpers\Rules;

use Exception;
use PHPUnit\Framework\Assert;
use PhpLint\Linter\LintContext;
use PhpLint\Linter\LintResult;

class RuleRejectionAssertion extends AbstractRuleAssertion
{
    /**
     * @inheritdoc
     */
    protected function assertAst($ast)
    {
        $assertionMessage = sprintf(
            "Failed asserting that rule \"%s\" rejects code:\n\n%s",
            $this->getRule()->getDescription()->getIdentifier(),
            $this->getTestCode()
        );

        $context = new LintContext();
        $lintResult = new LintResult();

        $this->getRule()->validate($ast, $context, $lintResult);
        Assert::assertGreaterThan(0, $lintResult->getViolations(), $assertionMessage);
    }

    /**
     * @param Exception $previousException
     * @throws RuleAssertionException
     */
    protected function throwAssertionException(Exception $previousException)
    {
        throw RuleAssertionException::rejectionTestFailed($this, $previousException);
    }
}
