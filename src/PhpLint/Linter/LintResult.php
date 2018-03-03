<?php
declare(strict_types=1);

namespace PhpLint\Linter;

class LintResult
{
    /**
     * @var RuleViolation[]
     */
    protected $violations = [];

    /**
     * @return RuleViolation[]
     */
    public function getViolations(): array
    {
        return $this->violations;
    }

    /**
     * @param TODO $node
     * @param string $messageId
     */
    public function reportViolation($node, string $messageId)
    {
        $this->violations[] = new RuleViolation($node, $messageId);
    }
}
