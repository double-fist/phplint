<?php
declare(strict_types=1);

namespace PhpLint\Linter;

use PhpLint\Ast\SourceLocation;

class RuleViolation
{
    /**
     * @var SourceLocation
     */
    protected $location;

    /**
     * @var string
     */
    protected $ruleName;

    /**
     * @var string
     */
    protected $severity;

    /**
     * @var string
     */
    protected $message;

    /**
     * @param SourceLocation $location
     * @param string $ruleName
     * @param string $severity
     * @param string $message
     */
    public function __construct(SourceLocation $location, string $ruleName, string $severity, string $message)
    {
        $this->location = $location;
        $this->ruleName = $ruleName;
        $this->severity = $severity;
        $this->message = $message;
    }

    /**
     * @return SourceLocation
     */
    public function getLocation(): SourceLocation
    {
        return $this->location;
    }

    /**
     * @return string
     */
    public function getRuleName(): string
    {
        return $this->ruleName;
    }

    /**
     * @return string
     */
    public function getSeverity(): string
    {
        return $this->severity;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
