<?php
declare(strict_types=1);

namespace PhpLint\Test\PhpParser;

use PhpLint\AbstractParser;
use PhpLint\Ast\AstNode;
use PhpLint\Ast\AstNodeType;
use PhpLint\PhpParser\ParserContext;
use PhpLint\PhpParser\PhpParser;
use PhpLint\TestHelpers\AstTestCase;

class PhpParserTest extends AstTestCase
{
    /** @var PhpParser */
    protected $parser = null;

    protected function setUp()
    {
        $this->parser = AbstractParser::create();
    }

    public function testCreatingAParser()
    {
        $parser = AbstractParser::create();

        $this->assertNotNull($parser);
        $this->assertInstanceOf(PhpParser::class, $parser);
    }

    public function testSourceContextHasAPath()
    {
        $program = <<<PROGRAM
<?php

PROGRAM;

        $sourceContext = $this->parser->parse($program, 'test.php');

        $this->assertInstanceOf(ParserContext::class, $sourceContext);
        $this->assertEquals('test.php', $sourceContext->getPath());
    }

    public function testSourceRootGetsParsed()
    {
        $program = <<<PROGRAM
<?php

PROGRAM;

        $sourceContext = $this->parser->parse($program, 'test.php');

        $sourceRoot = $sourceContext->getAst();
        $this->assertNodeType(AstNodeType::SOURCE_ROOT, $sourceRoot);
    }

    public function testParsingANamespaceDeclaration()
    {
        $program = <<<PROGRAM
<?php
namespace PhpLint\Test\Ast;
PROGRAM;

        $sourceContext = $this->parser->parse($program, 'test.php');

        /** @var AstNode[] $sourceContents */
        $sourceContents = $sourceContext->getAst()->get('contents');
        $this->assertCount(1, $sourceContents);

        /** @var AstNode $namespaceNode */
        $namespaceNode = $sourceContents[0];
        $this->assertNodeType(AstNodeType::NAMESPACE, $namespaceNode);

        /** @var AstNode $nameNode */
        $nameNode = $namespaceNode->get('name');
        $this->assertNodeType(AstNodeType::NAME, $nameNode);
        $this->assertEquals(['PhpLint', 'Test', 'Ast'], $nameNode->get('parts'));
    }

    public function testParsingANamespacedClass()
    {
        $program = <<<PROGRAM
<?php
namespace PhpLint\Test\Ast;

class Test
{
}
PROGRAM;

        $sourceContext = $this->parser->parse($program, 'test.php');

        /** @var AstNode[] $sourceContents */
        $sourceContents = $sourceContext->getAst()->get('contents');
        $this->assertCount(1, $sourceContents);

        /** @var AstNode $namespaceNode */
        $namespaceNode = $sourceContents[0];
        $this->assertNodeType(AstNodeType::NAMESPACE, $namespaceNode);

        /** @var AstNode $nameNode */
        $nameNode = $namespaceNode->get('name');
        $this->assertNodeType(AstNodeType::NAME, $nameNode);
        $this->assertEquals(['PhpLint', 'Test', 'Ast'], $nameNode->get('parts'));
    }

    public function testCallGetChildrenOnAParsedNamespace()
    {
        $program = <<<PROGRAM
<?php
namespace PhpLint\Test\Ast;

class Test
{
}
PROGRAM;

        $sourceContext = $this->parser->parse($program, 'test.php');
        $children = $sourceContext->getAst()->get('contents')[0]->getChildren();

        $this->assertCount(2, $children);
        $this->assertNodeType(AstNodeType::NAME, $children[0]);
        $this->assertNodeType(AstNodeType::CLASS_DECLARATION, $children[1]);
    }
}
