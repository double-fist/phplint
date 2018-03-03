<?php
declare(strict_types=1);

namespace PhpLint\Linter;

use PhpLint\Ast\AstNode;

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
     * @param AstNode $node
     * @param string $messageId
     */
    public function reportViolation(AstNode $node, string $messageId)
    {
        $this->violations[] = new RuleViolation($node, $messageId);
    }
}
