<?php
declare(strict_types=1);

namespace PhpLint\TestHelpers\Rules;

use Exception;
use PhpLint\Ast\AstNode;
use PhpLint\Ast\SourceContext;
use PhpLint\PhpParser\PhpParser;
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
     * @var PhpParser
     */
    protected $parser;

    /**
     * @param Rule $rule
     * @param string $testCode
     */
    public function __construct(Rule $rule, string $testCode)
    {
        $this->rule = $rule;
        $this->testCode = $testCode;
        $this->parser = new PhpParser();
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
     * @return PhpParser
     */
    public function getParser(): PhpParser
    {
        return $this->parser;
    }

    /**
     * Executes the assertion of this test.
     */
    public function assert()
    {
        try {
            $code = $this->getTestCode();
            $sourceContext = $this->getParser()->parse($code);
            $this->doAssert($sourceContext);
        } catch (ExpectationFailedException $exception) {
            // Failed assertion
            throw $exception;
        } catch (Exception $exception) {
            // Other error
            $this->throwAssertionException($exception);
        }
    }

    /**
     * Implement the actual assertions here.
     *
     * @param SourceContext $sourceContext
     */
    abstract protected function doAssert(SourceContext $sourceContext);

    /**
     * @param Exception $previousException
     * @throws RuleAssertionException
     */
    abstract protected function throwAssertionException(Exception $previousException);
}
