<?php
declare(strict_types=1);

namespace PhpLint\Linter;

use PhpLint\Ast\AstNode;

class RuleViolation
{
    /**
     * @var AstNode $node
     */
    protected $node;

    /**
     * @var string $messageId
     */
    protected $messageId;

    /**
     * @param AstNode $node
     * @param string $messageId
     */
    public function __construct(AstNode $node, string $messageId)
    {
        $this->node = $node;
        $this->messageId = $messageId;
    }

    /**
     * @return AstNode $node
     */
    public function getNode(): AstNode
    {
        $this->node;
    }

    /**
     * @return string $messageId
     */
    public function getMessageId(): string
    {
        $this->messageId;
    }
}
