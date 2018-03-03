<?php
declare(strict_types=1);

namespace PhpLint\TestHelpers\Rules;

use Exception;
use PhpLint\Ast\AstNode;
use PhpLint\Linter\LintContext;
use PhpLint\Linter\LintResult;
use PhpLint\Rules\Rule;
use PHPUnit\Framework\Assert;

class RuleFixAssertion extends AbstractRuleAssertion
{
    /**
     * @var string
     */
    protected $fixedCode;

    /**
     * @param Rule $rule
     * @param string $testCode
     */
    public function __construct(Rule $rule, string $testCode, string $fixedCode)
    {
        parent::__construct($rule, $testCode);
        $this->fixedCode = $fixedCode;
    }

    /**
     * @inheritdoc
     */
    protected function assertAst(AstNode $ast)
    {
        $assertionMessage = sprintf(
            "Failed asserting that rule \"%s\" fixes code:\n\n%s",
            $this->getRule()->getDescription()->getIdentifier(),
            $this->getTestCode()
        );

        $context = new LintContext();
        $lintResult = new LintResult();

        $this->getRule()->validate($ast, $context, $lintResult);
        Assert::assertGreaterThan(0, $lintResult->getViolations(), $assertionMessage);

        // TODO: Apply the proposed fixes and assert the results
        $expectedAst = $this->createAst($this->fixedCode);
    }

    /**
     * @param Exception $previousException
     * @throws RuleAssertionException
     */
    protected function throwAssertionException(Exception $previousException)
    {
        throw RuleAssertionException::fixTestFailed($this, $previousException);
    }
}
