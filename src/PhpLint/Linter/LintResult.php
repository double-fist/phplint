<?php
declare(strict_types=1);

namespace PhpLint\Linter;

use Countable;
use PhpLint\Ast\AstNode;
use PhpLint\Rules\RuleSeverity;

class LintResult implements Countable
{
    /**
     * @var bool
     */
    private $errorsOnly;

    /**
     * @var RuleViolation[]
     */
    protected $violations = [];

    /**
     * @param bool $errorsOnly
     */
    public function __construct(bool $errorsOnly = false)
    {
        $this->errorsOnly = $errorsOnly;
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return count($this->violations);
    }

    /**
     * @return RuleViolation[]
     */
    public function getViolations(): array
    {
        return $this->violations;
    }

    /**
     * @param string $ruleName
     * @param string|int $severity
     * @param string $messageId
     * @param AstNode $node
     * @throws LintException if the passed $severity is invalid.
     */
    public function reportViolation(string $ruleName, $severity, string $messageId, AstNode $node)
    {
        if (!RuleSeverity::isRuleSeverity($severity)) {
            throw LintException::invalidRuleSeverityReported($ruleName, $severity);
        }

        $errorSeverityCode = array_search(RuleSeverity::SEVERITY_ERROR, RuleSeverity::ALL_SEVERITIES);
        if ($this->errorsOnly && RuleSeverity::getRuleSeverity($severity) < $errorSeverityCode) {
            return;
        }

        $this->violations[] = new RuleViolation(
            $ruleName,
            RuleSeverity::getRuleSeverity($severity, true),
            $messageId,
            $node
        );
    }
}
