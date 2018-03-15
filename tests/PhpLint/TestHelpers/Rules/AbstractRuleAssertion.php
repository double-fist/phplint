<?php
declare(strict_types=1);

namespace PhpLint\TestHelpers\Rules;

use Exception;
use PhpLint\Ast\AstNodeTraverser;
use PhpLint\Ast\SourceContext;
use PhpLint\Configuration\Configuration;
use PhpLint\PhpParser\ParserContext;
use PhpLint\Linter\LintResult;
use PhpLint\Linter\RuleViolation;
use PhpLint\PhpParser\PhpParser;
use PhpLint\Rules\Rule;
use PHPUnit\Framework\Assert;
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
     * @param SourceContext $sourceContext
     * @param LintResult $lintResult
     */
    protected function validateRule(SourceContext $sourceContext, LintResult $lintResult)
    {
        $nodeTraverser = new AstNodeTraverser($sourceContext->getAst());
        foreach ($nodeTraverser as $node) {
            if ($this->getRule()->canValidateNode($node)) {
                $this->getRule()->validate($node, $sourceContext, Configuration::RULE_SEVERITY_ERROR, $lintResult);
            }
        }
    }

    /**
     * Asserts that all message IDs contained in the given $lintResult are defined in the rule's description.
     *
     * @param LintResult $lintResult
     */
    protected function assertLintResult(LintResult $lintResult)
    {
        $definedMessages = $this->getRule()->getDescription()->getMessages();
        $usedMessageIds = array_map(
            function (RuleViolation $violation) {
                return $violation->getMessageId();
            },
            $lintResult->getViolations()
        );

        foreach ($usedMessageIds as $messageId) {
            Assert::assertArrayHasKey(
                $messageId,
                $definedMessages,
                sprintf(
                    'Failed asserting that the message ID "%s" used by rule "%s" to report a violation is known to that rule.',
                    $messageId,
                    $this->getRule()->getDescription()->getIdentifier()
                )
            );
        }
    }

    /**
     * Implement the actual assertions here.
     *
     * @param ParserContext $sourceContext
     */
    abstract protected function doAssert(ParserContext $sourceContext);

    /**
     * @param Exception $previousException
     * @throws RuleAssertionException
     */
    abstract protected function throwAssertionException(Exception $previousException);
}
