<?php
declare(strict_types=1);

namespace PhpLint\PhpParser;

use PhpLint\Ast\AstNode;
use PhpLint\Ast\SourceContext;
use PhpLint\Ast\SourceRange;
use PhpLint\Ast\Token;

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
     * @param AstNode $ast
     * @param array $tokens
     * @param string $content
     * @param null|string $path
     */
    public function __construct(AstNode $ast, array $tokens, string $content, string $path = null)
    {
        $this->path = $path;
        $this->content = $content;
        $this->tokens = $tokens;
        $this->ast = $ast;
    }

    /**
     * @return null|string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return AstNode
     */
    public function getAst(): AstNode
    {
        return $this->ast;
    }

    public function getSourceRangeOfNode(AstNode $node): SourceRange
    {
        if (!$node instanceof ParserAstNode) {
            throw new \InvalidArgumentException(
                'Argument of type %s expected, got %s instead.',
                ParserAstNode::class,
                get_class($node)
            );
        }

        $wrappedNode = $node->getWrappedNode();

        if ($wrappedNode === null) {
            return $this->getSourceRangeOfTokens(0, count($this->tokens) - 1);
        }

        return $this->getSourceRangeOfTokens(
            $wrappedNode->getStartTokenPos(),
            $wrappedNode->getEndTokenPos()
        );
    }

    protected function getSourceRangeOfTokens(int $startTokenIndex, int $endTokenIndex): SourceRange
    {
        $startToken = $this->tokens[$startTokenIndex];
        $endToken = $this->tokens[$endTokenIndex];

        return SourceRange::spanningRanges($startToken->getSourceRange(), $endToken->getSourceRange());
    }
}
