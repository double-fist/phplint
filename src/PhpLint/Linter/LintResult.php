<?php
declare(strict_types=1);

namespace PhpLint\Linter;

use Countable;
use PhpLint\Ast\AstNode;

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
     * @param AstNode $node
     * @param string $messageId
     */
    public function reportViolation(AstNode $node, string $messageId)
    {
        $this->violations[] = new RuleViolation($node, $messageId);
    }
}
