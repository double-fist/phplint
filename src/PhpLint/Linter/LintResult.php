<?php
declare(strict_types=1);

namespace PhpLint\Linter;

use Countable;
use PhpLint\Ast\SourceLocation;
use PhpLint\Linter\Directive\DisableDirective;
use PhpLint\Rules\Rule;
use PhpLint\Rules\RuleSeverity;

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
     * @return bool
     */
    public function containsErrors(): bool
    {
        foreach ($this->violations as $violation) {
            if ($violation->getSeverity() === RuleSeverity::SEVERITY_ERROR) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return RuleViolation[]
     */
    public function getViolations(): array
    {
        return $this->violations;
    }

    /**
     * @param Rule $rule
     * @param string $message
     * @param SourceLocation $location
     * @throws LintException if the $rule's severity is invalid.
     */
    public function reportViolation(
        Rule $rule,
        string $message,
        SourceLocation $location
    ) {
        if (!RuleSeverity::isRuleSeverity($rule->getSeverity())) {
            throw LintException::invalidRuleSeverityReported(
                $rule->getDescription()->getIdentifier(),
                $rule->getSeverity()
            );
        }

        // Filter out warnings, if only errors should be collected
        $errorSeverityCode = array_search(RuleSeverity::SEVERITY_ERROR, RuleSeverity::ALL_SEVERITIES);
        if ($this->errorsOnly && RuleSeverity::getRuleSeverity($rule->getSeverity()) < $errorSeverityCode) {
            return;
        }

        $this->violations[] = new RuleViolation(
            $location,
            $rule->getDescription()->getIdentifier(),
            $rule->getSeverity(),
            $message
        );
    }

    /**
     * @param DisableDirective[] $disableDirectives
     */
    public function applyDisableDirectives(array $disableDirectives)
    {
        // Sort the violations by location
        usort(
            $this->violations,
            function (RuleViolation $lhs, RuleViolation $rhs) {
                return $lhs->getLocation()->compare($rhs->getLocation());
            }
        );

        $directiveIndex = 0;
        $currentGlobalDisableDirective = null;
        $disabledRules = [];
        $enabledRules = [];

        $filteredViolations = [];
        foreach ($this->violations as $violation) {
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

        $this->violations = $filteredViolations;
    }
}
