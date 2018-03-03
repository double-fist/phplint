<?php
declare(strict_types=1);

namespace PhpLint\TestHelpers\Rules;

use Exception;
use PhpLint\Ast\AstNode;
use PhpLint\Linter\LintContext;
use PhpLint\Linter\LintResult;
use PHPUnit\Framework\Assert;

class RuleAcceptanceAssertion extends AbstractRuleAssertion
{
    /**
     * @inheritdoc
     */
    protected function assertAst(AstNode $ast)
    {
        $assertionMessage = sprintf(
            "Failed asserting that rule \"%s\" accepts code:\n\n%s",
            $this->getRule()->getDescription()->getIdentifier(),
            $this->getTestCode()
        );

        $context = new LintContext();
        $lintResult = new LintResult();

        $this->getRule()->validate($ast, $context, $lintResult);
        Assert::assertCount(0, $lintResult->getViolations(), $assertionMessage);
    }

    /**
     * @param Exception $previousException
     * @throws RuleAssertionException
     */
    protected function throwAssertionException(Exception $previousException)
    {
        throw RuleAssertionException::acceptanceTestFailed($this, $previousException);
    }
}
