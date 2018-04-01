<?php
declare(strict_types=1);

namespace PhpLint\PhpParser;

use PhpLint\Ast\Node\SourceRoot;
use PhpLint\Ast\SourceContext;
use PhpLint\Ast\SourceRange;
use PhpLint\Ast\Token;
use PhpParser\Node;

class ParserContext implements SourceContext
{
    /** @var string|null the path of the source unit, if applicable */
    private $path = null;

    private $content;

    /** @var Token[] */
    private $tokens;

    private $ast;

    /**
     * SourceContext constructor.
     * @param SourceRoot $ast
     * @param array $tokens
     * @param string $content
     * @param null|string $path
     */
    public function __construct(SourceRoot $ast, array $tokens, string $content, string $path = null)
    {
        $this->path = $path;
        $this->content = $content;
        $this->tokens = $tokens;
        $this->ast = $ast;
    }

    /**
     * @inheritdoc
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @inheritdoc
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @inheritdoc
     */
    public function getAst(): SourceRoot
    {
        return $this->ast;
    }

    /**
     * @inheritdoc
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }

    /**
     * @inheritdoc
     */
    public function getSourceRangeOfNode(Node $node): SourceRange
    {
        if ($node === $this->ast) {
            return $this->getSourceRangeOfTokens(0, count($this->tokens) - 1);
        }

        return $this->getSourceRangeOfTokens(
            $node->getStartTokenPos(),
            $node->getEndTokenPos()
        );
    }

    /**
     * @param int $startTokenIndex
     * @param int $endTokenIndex
     * @return SourceRange
     */
    protected function getSourceRangeOfTokens(int $startTokenIndex, int $endTokenIndex): SourceRange
    {
        $startToken = $this->tokens[$startTokenIndex];
        $endToken = $this->tokens[$endTokenIndex];

        return SourceRange::spanningRanges($startToken->getSourceRange(), $endToken->getSourceRange());
    }
}
