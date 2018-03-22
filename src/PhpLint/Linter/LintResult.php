<?php
declare(strict_types=1);

namespace PhpLint\Linter;

use Countable;
use PhpLint\Ast\SourceContext;
use PhpLint\Ast\SourceLocation;
use PhpLint\Rules\Rule;
use PhpLint\Rules\RuleSeverity;
use PhpParser\Node;

class LintResult implements Countable
{
    /**
     * @var bool
     */
    private $errorsOnly;

    /**
     * @var array
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
     * @return string[]
     */
    public function getFilenames(): array
    {
        return array_keys($this->violations);
    }

    /**
     * @param string $filename
     * @return RuleViolation[]
     */
    public function getViolations(string $filename = null): array
    {
        if ($filename !== null) {
            return $this->violations[$filename];
        } elseif (count($this->violations) > 0) {
            return array_merge(...array_values($this->violations));
        } else {
            return [];
        }
    }

    /**
     * @param Rule $rule
     * @param string|int $severity
     * @param string $messageId
     * @param SourceLocation $location
     * @param SourceContext $context
     * @throws LintException if the passed $severity is invalid.
     */
    public function reportViolation(
        Rule $rule,
        $severity,
        string $messageId,
        SourceLocation $location,
        SourceContext $context
    ) {
        if (!RuleSeverity::isRuleSeverity($severity)) {
            throw LintException::invalidRuleSeverityReported($rule->getName(), $severity);
        }

        // Filter out warnings, if only errors should be collected
        $errorSeverityCode = array_search(RuleSeverity::SEVERITY_ERROR, RuleSeverity::ALL_SEVERITIES);
        if ($this->errorsOnly && RuleSeverity::getRuleSeverity($severity) < $errorSeverityCode) {
            return;
        }

        $violation = new RuleViolation(
            $location,
            $rule->getName(),
            RuleSeverity::getRuleSeverity($severity, true),
            $rule->getDescription()->getMessage($messageId)
        );

        $filename = $context->getPath() ?? '';
        if (!isset($this->violations[$filename])) {
            $this->violations[$filename] = [];
        }
        $this->violations[$filename][] = $violation;
    }
}
