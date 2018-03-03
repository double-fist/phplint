<?php
declare(strict_types=1);

namespace PhpLint\PhpParser;

use PhpLint\AbstractParser;
use PhpLint\Ast\AstNode;
use PhpLint\Ast\AstNodeType;
use PhpLint\Ast\SourceContext;
use PhpParser\Lexer;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
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

        $astRoot = new PhpParserAstNode(
            AstNodeType::SOURCE_ROOT,
            [
                'contents' => $this->transformPhpParserNodes($parserResult),
            ]
        );

        return new PhpParserSourceContext(
            $astRoot,
            $this->lexer->getTokens(),
            $code,
            $path
        );
    }

    private function transformPhpParserNode($parserNode): AstNode
    {
        switch (true) {
            case $parserNode instanceof Namespace_:
                return new PhpParserAstNode(
                    AstNodeType::NAMESPACE,
                    [
                        'name' => $this->transformPhpParserNode($parserNode->name),
                        'statements' => $this->transformPhpParserNodes($parserNode->stmts),
                    ],
                    $parserNode
                );

            case $parserNode instanceof Name:
                return new PhpParserAstNode(
                    AstNodeType::NAME,
                    [
                        'parts' => $parserNode->parts,
                    ],
                    $parserNode
                );

            case $parserNode instanceof Class_:
                return new PhpParserAstNode(
                    AstNodeType::CLASS_DECLARATION,
                    [
                        'name' => $parserNode->name,
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
}
