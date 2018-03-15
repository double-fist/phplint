<?php
declare(strict_types=1);

namespace PhpLint\Linter;

use PhpLint\Ast\AstNode;

class RuleViolation
{
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
    protected $messageId;

    /**
     * @var AstNode
     */
    protected $node;

    /**
     * @param string $ruleName
     * @param string $severity
     * @param string $messageId
     * @param AstNode $node
     */
    public function __construct(string $ruleName, string $severity, string $messageId, AstNode $node)
    {
        $this->ruleName = $ruleName;
        $this->severity = $severity;
        $this->messageId = $messageId;
        $this->node = $node;
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
    public function getMessageId(): string
    {
        return $this->messageId;
    }

    /**
     * @return AstNode
     */
    public function getNode(): AstNode
    {
        return $this->node;
    }
}
