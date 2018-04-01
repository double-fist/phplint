<?php
declare(strict_types=1);

namespace PhpLint\Linter;

use Countable;
use PhpLint\Ast\SourceContext;
use PhpLint\Ast\SourceLocation;
use PhpLint\Linter\Directive\DisableDirective;
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
        return count($this->getViolations());
    }

    /**
     * @return bool
     */
    public function containsErrors(): bool
    {
        foreach ($this->getViolations() as $violation) {
            if ($violation->getSeverity() === RuleSeverity::SEVERITY_ERROR) {
                return true;
            }
        }

        return false;
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
     * @param string $message
     * @param SourceLocation $location
     * @param SourceContext $context
     * @throws LintException if the passed $severity is invalid.
     */
    public function reportViolation(
        Rule $rule,
        $severity,
        string $message,
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
            $message
        );

        $filename = $context->getPath() ?? '';
        if (!isset($this->violations[$filename])) {
            $this->violations[$filename] = [];
        }
        $this->violations[$filename][] = $violation;
    }

    /**
     * @param string|null $filename
     * @param DisableDirective[] $disableDirectives
     */
    public function applyDisableDirectives($filename, array $disableDirectives)
    {
        if ($filename === null) {
            $filename = '';
        }
        if (!isset($this->violations[$filename])) {
            return;
        }

        // Sort the file violations by location
        $violations = $this->violations[$filename];
        usort(
            $violations,
            function (RuleViolation $lhs, RuleViolation $rhs) {
                return $lhs->getLocation()->compare($rhs->getLocation());
            }
        );

        $directiveIndex = 0;
        $currentGlobalDisableDirective = null;
        $disabledRules = [];
        $enabledRules = [];

        $filteredViolations = [];
        foreach ($violations as $violation) {
            while ($directiveIndex < count($disableDirectives)
                && $disableDirectives[$directiveIndex]->getSourceLocation()->isSmallerThanOrEquals($violation->getLocation())
            ) {
                $directive = $disableDirectives[$directiveIndex];
                $directiveIndex += 1;

                switch ($directive->getType()) {
                    case DisableDirective::TYPE_DISABLE:
                        if ($directive->getRuleId() === null) {
                            // Global disable directive
                            $currentGlobalDisableDirective = $directive;
                            $disabledRules = [];
                            $enabledRules = [];
                        } elseif ($currentGlobalDisableDirective) {
                            $disabledRules[$directive->getRuleId()] = $directive;
                            unset($enabledRules[$directive->getRuleId()]);
                        } else {
                            $disabledRules[$directive->getRuleId()] = $directive;
                        }
                        break;
                    case DisableDirective::TYPE_ENABLE:
                        if ($directive->getRuleId() === null) {
                            // Global enable directive
                            $currentGlobalDisableDirective = null;
                            $disabledRules = [];
                        } elseif ($currentGlobalDisableDirective) {
                            unset($disabledRules[$directive->getRuleId()]);
                            $enabledRules[$directive->getRuleId()] = true;
                        } else {
                            unset($disabledRules[$directive->getRuleId()]);
                        }
                        break;
                    default:
                        break;
                }
            }

            if (!isset($disabledRules[$violation->getRuleId()]) && ($currentGlobalDisableDirective === null || isset($enabledRules[$violation->getRuleId()]))) {
                $filteredViolations[] = $violation;
            }
        }

        if (count($filteredViolations) > 0) {
            $this->violations[$filename] = $filteredViolations;
        } else {
            unset($this->violations[$filename]);
        }
    }
}
