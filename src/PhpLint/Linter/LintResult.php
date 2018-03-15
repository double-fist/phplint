<?php
declare(strict_types=1);

namespace PhpLint\Linter;

use Countable;
use PhpLint\Ast\AstNode;
use PhpLint\Rules\RuleSeverity;

class LintResult implements Countable
{
    /**
     * @var RuleViolation[]
     */
    protected $violations = [];

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

        $this->violations[] = new RuleViolation(
            $ruleName,
            RuleSeverity::getRuleSeverity($severity, true),
            $messageId,
            $node
        );
    }
}
