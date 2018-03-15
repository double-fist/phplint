<?php
declare(strict_types=1);

namespace PhpLint\TestHelpers\Rules;

use Exception;
use PhpLint\Ast\AstNode;
use PhpLint\PhpParser\ParserContext;
use PhpLint\Linter\LintResult;
use PHPUnit\Framework\Assert;

class RuleRejectionAssertion extends AbstractRuleAssertion
{
    /**
     * @inheritdoc
     */
    protected function doAssert(ParserContext $sourceContext)
    {
        $assertionMessage = sprintf(
            "Failed asserting that rule \"%s\" rejects code:\n\n%s\n",
            $this->getRule()->getDescription()->getIdentifier(),
            $this->getTestCode()
        );

        $lintResult = new LintResult();

        $this->validateRule($sourceContext, $lintResult);
        Assert::assertNotEmpty($lintResult->getViolations(), $assertionMessage);
        $this->assertLintResult($lintResult);
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
