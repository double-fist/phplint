<?php
declare(strict_types=1);

namespace PhpLint\TestHelpers\Rules;

use Exception;
use PhpLint\Rules\Rule;
use PHPUnit\Framework\ExpectationFailedException;

abstract class AbstractRuleAssertion
{
    /**
     * @var Rule
     */
    protected $rule;

    /**
     * @var string
     */
    protected $testCode;

    /**
     * @param Rule $rule
     * @param string $testCode
     */
    public function __construct(Rule $rule, string $testCode)
    {
        $this->rule = $rule;
        $this->testCode = $testCode;
    }

    /**
     * @return Rule
     */
    public function getRule(): Rule
    {
        return $this->rule;
    }

    /**
     * @return string
     */
    public function getTestCode(): string
    {
        return $this->testCode;
    }

    /**
     * Executes the assertion of this test.
     */
    public function assert()
    {
        try {
            $code = $this->getTestCode();
            $ast = $this->createAst($code);
            $this->assertAst($ast);
        } catch (ExpectationFailedException $exception) {
            // Failed assertion
            throw $exception;
        } catch (Exception $exception) {
            // Other error
            $this->throwAssertionException($exception);
        }
    }

    /**
     * @param string $code
     * @return TODO
     */
    protected function createAst(string $code)
    {
        // TODO: Create AST
        return [];
    }

    /**
     * Implement the actual assertions here.
     *
     * @param TODO $ast
     */
    abstract protected function assertAst($ast);

    /**
     * @param Exception $previousException
     * @throws RuleAssertionException
     */
    abstract protected function throwAssertionException(Exception $previousException);
}
