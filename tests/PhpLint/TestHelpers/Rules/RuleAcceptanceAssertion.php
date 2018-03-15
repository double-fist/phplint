<?php
declare(strict_types=1);

namespace PhpLint\TestHelpers\Rules;

use Exception;
use PhpLint\Ast\AstNode;
use PhpLint\PhpParser\ParserContext;
use PhpLint\Linter\LintResult;
use PHPUnit\Framework\Assert;

class RuleAcceptanceAssertion extends AbstractRuleAssertion
{
    /**
     * @inheritdoc
     */
    protected function doAssert(ParserContext $sourceContext)
    {
        $assertionMessage = sprintf(
            "Failed asserting that rule \"%s\" accepts code:\n\n%s\n",
            $this->getRule()->getDescription()->getIdentifier(),
            $this->getTestCode()
        );

        $lintResult = new LintResult();

        $this->recursivelyValidateRule($sourceContext->getAst(), $sourceContext, $lintResult);
        Assert::assertEmpty($lintResult->getViolations(), $assertionMessage);
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
