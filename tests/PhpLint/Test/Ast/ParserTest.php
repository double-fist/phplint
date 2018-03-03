<?php
namespace PhpLint\Test\Ast;

use PhpLint\AbstractParser;
use PhpLint\Ast\AstNode;
use PhpLint\Ast\AstNodeType;
use PhpLint\PhpParser\PhpParser;
use PhpLint\TestHelpers\AstTestCase;

class ParserTest extends AstTestCase
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

    public function testParsingAnEmptyProgram()
    {
        $program = <<<PROGRAM
<?php

PROGRAM;

        $node = $this->parser->parse('test.php', $program);

        $this->assertInstanceOf(AstNode::class, $node);
        $this->assertEquals(AstNodeType::SOURCE_FILE, $node->getType());
        $this->assertEquals('test.php', $node->get('path'));
        $this->assertCount(0, $node->get('contents'));
    }

    public function testParsingANamespaceDeclaration()
    {
        $program = <<<PROGRAM
<?php
namespace PhpLint\Test\Ast;
PROGRAM;

        $node = $this->parser->parse('test.php', $program);

        $this->assertEquals(AstNodeType::SOURCE_FILE, $node->getType());
        $contents = $node->get('contents');

        $this->assertCount(1, $contents);
        /** @var AstNode $namespaceNode */
        $namespaceNode = $contents[0];
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

        $node = $this->parser->parse('test.php', $program);

        $this->assertEquals(AstNodeType::SOURCE_FILE, $node->getType());
        $contents = $node->get('contents');

        $this->assertCount(1, $contents);
        /** @var AstNode $namespaceNode */
        $namespaceNode = $contents[0];
        $this->assertNodeType(AstNodeType::NAMESPACE, $namespaceNode);

        /** @var AstNode $nameNode */
        $nameNode = $namespaceNode->get('name');
        $this->assertNodeType(AstNodeType::NAME, $nameNode);
        $this->assertEquals(['PhpLint', 'Test', 'Ast'], $nameNode->get('parts'));
    }
}
