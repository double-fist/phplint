<?php
declare(strict_types=1);

namespace PhpLint\PhpParser;

use PhpLint\Ast\AstNode;
use PhpLint\Ast\SourceContext;
use PhpLint\Ast\SourceRange;

class PhpParserSourceContext implements SourceContext
{
    /** @var string|null the path of the source unit, if applicable */
    private $path = null;

    private $content;

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
        // TODO
        return null;
    }
}
