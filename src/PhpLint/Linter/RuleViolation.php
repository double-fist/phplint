<?php
declare(strict_types=1);

namespace PhpLint\Linter;

class RuleViolation
{
    /**
     * @var TODO $node
     */
    protected $node;

    /**
     * @var string $messageId
     */
    protected $messageId;

    /**
     * @param TODO $node
     * @param string $messageId
     */
    public function __construct($node, string $messageId)
    {
        $this->node = $node;
        $this->messageId = $messageId;
    }

    /**
     * @return TODO $node
     */
    public function getNode()
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
