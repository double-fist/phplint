<?php
declare(strict_types=1);

namespace PhpLint\PhpParser;

use PhpLint\AbstractParser;
use PhpLint\Ast\AstNode;
use PhpLint\Ast\AstNodeType;
use PhpLint\Ast\SourceContext;
use PhpLint\Ast\SourceLocation;
use PhpLint\Ast\SourceRange;
use PhpLint\Ast\Token;
use PhpParser\Lexer;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar;
use PhpParser\Node\Stmt;
use PhpParser\ParserFactory;

class PhpParser extends AbstractParser
{
    /** @var \PhpParser\Parser $phpParser */
    private $phpParser;

    private $lexer;

    public function __construct()
    {
        $parserFactory = new ParserFactory();
        $this->lexer = new Lexer(
            [
                'usedAttributes' => [
                    'comments',
                    'startLine',
                    'endLine',
                    'startTokenPos',
                    'endTokenPos',
                    'startFilePos',
                    'endFilePos',
                ],
            ]
        );
        $this->phpParser = $parserFactory->create(ParserFactory::PREFER_PHP7, $this->lexer);
    }

    public function parse(string $code, string $path = null): SourceContext
    {
        $parserResult = $this->phpParser->parse($code);

        $astRoot = new ParserAstNode(
            AstNodeType::SOURCE_ROOT,
            [
                'contents' => $this->transformPhpParserNodes($parserResult),
            ]
        );

        $tokens = $this->transformTokens($this->lexer->getTokens());

        return new ParserContext(
            $astRoot,
            $tokens,
            $code,
            $path
        );
    }

    private function transformPhpParserNode($parserNode): AstNode
    {
        switch (true) {
            case $parserNode instanceof Stmt\Class_:
                return new ParserAstNode(
                    AstNodeType::CLASS_DECLARATION,
                    [
                        'name' => $parserNode->name,
                        'statements' => $this->transformPhpParserNodes($parserNode->stmts),
                    ],
                    $parserNode
                );

            case $parserNode instanceof Stmt\ClassConst:
                return new ParserAstNode(
                    AstNodeType::CLASS_CONST,
                    [
                        'name' => $parserNode->consts[0]->name,
                        'value' => $parserNode->consts[0]->value,
                    ],
                    $parserNode
                );

            case $parserNode instanceof Stmt\ClassMethod:
                return new ParserAstNode(
                    AstNodeType::CLASS_METHOD,
                    [
                        'name' => $parserNode->name,
                    ],
                    $parserNode
                );

            case $parserNode instanceof Name:
                return new ParserAstNode(
                    AstNodeType::NAME,
                    [
                        'parts' => $parserNode->parts,
                    ],
                    $parserNode
                );

            case $parserNode instanceof Stmt\Namespace_:
                return new ParserAstNode(
                    AstNodeType::NAMESPACE,
                    [
                        'name' => $this->transformPhpParserNode($parserNode->name),
                        'statements' => $this->transformPhpParserNodes($parserNode->stmts),
                    ],
                    $parserNode
                );

            case $parserNode instanceof Stmt\Expression:
                return new ParserAstNode(
                    AstNodeType::EXPRESSION,
                    [
                        'expr' => $this->transformPhpParserNode($parserNode->expr),
                    ],
                    $parserNode
                );

            case $parserNode instanceof Expr\Assign:
                return new ParserAstNode(
                    AstNodeType::ASSIGNMENT,
                    [
                        'var' => $this->transformPhpParserNode($parserNode->var),
                        'expr' => $this->transformPhpParserNode($parserNode->expr),
                    ],
                    $parserNode
                );

            case $parserNode instanceof Expr\Variable:
                return new ParserAstNode(
                    AstNodeType::VARIABLE,
                    [
                        'name' => $parserNode->name,
                    ],
                    $parserNode
                );

            case $parserNode instanceof Scalar\String_:
                return new ParserAstNode(
                    AstNodeType::STRING,
                    [
                        'value' => $parserNode->value,
                    ],
                    $parserNode
                );

            default:
                throw new \InvalidArgumentException(sprintf('Unknown node type %s', get_class($parserNode)));
        }
    }

    private function transformPhpParserNodes(array $parserResults): array
    {
        return array_map(function ($parserResult) {
            return $this->transformPhpParserNode($parserResult);
        }, $parserResults);
    }

    /**
     * @param array $tokens
     * @return Token[]
     */
    private function transformTokens($tokens): array
    {
        $line = 1;
        $column = 1;

        $transformedTokens = [];

        foreach ($tokens as $token) {
            if (is_string($token)) {
                $transformedTokens[] = new Token(
                    'T_CHARACTER',
                    $token,
                    SourceRange::between(
                        SourceLocation::atLineAndColumn($line, $column),
                        SourceLocation::atLineAndColumn($line, $column)
                    )
                );
                $column += 1;
            } else {
                $tokenType = $token[0];
                $tokenValue = $token[1];
                $line = $token[2];

                // Compute the number of extra lines spanned by the token (a 2-line token spans 1 line)
                $extraSpannedLines = mb_substr_count($tokenValue, "\n") ?: 0;

                // Compute the end column in a multiline-safe manner
                $partFromLastNewline = mb_strrchr($tokenValue, "\n");
                $endColumn = $partFromLastNewline ? mb_strlen($partFromLastNewline) - 1 : $column + mb_strlen($tokenValue) - 1;
                $transformedTokens[] = new Token(
                    token_name($tokenType),
                    $tokenValue,
                    SourceRange::between(
                        SourceLocation::atLineAndColumn($line, $column),
                        SourceLocation::atLineAndColumn($line + $extraSpannedLines, $endColumn)
                    )
                );

                $line += $extraSpannedLines;
                $column = $endColumn + 1;
            }
        }

        return $transformedTokens;
    }
}
