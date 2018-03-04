<?php
declare(strict_types=1);

namespace PhpLint\Test\PhpParser;

use PhpLint\AbstractParser;
use PhpLint\Ast\AstNode;
use PhpLint\Ast\AstNodeType;
use PhpLint\Ast\SourceRange;
use PhpLint\PhpParser\PhpParser;
use PhpLint\TestHelpers\AstTestCase;

class SourceContextTest extends AstTestCase
{
    /** @var PhpParser */
    protected $parser = null;

    protected function setUp()
    {
        $this->parser = AbstractParser::create();
    }

    public function testLocatingANormalNamespaceDeclaration()
    {
        $program = <<<PROGRAM
<?php
namespace PhpLint\Test\Ast;
PROGRAM;

        $sourceContext = $this->parser->parse($program, 'test.php');
        $namespaceSourceRange = $sourceContext->getSourceRangeOfNode($sourceContext->getAst()->get('contents')[0]);

        $this->assertEquals('2:1-2:27', $namespaceSourceRange->__toString());

        /** @var AstNode $nameNode */
        $nameNode = $sourceContext->getAst()->get('contents')[0]->get('name');
        $this->assertNodeType(AstNodeType::NAME, $nameNode);
        $this->assertEquals(['PhpLint', 'Test', 'Ast'], $nameNode->get('parts'));
    }

    public function testLocatingANamespaceDeclarationWhichStartsAfterSomeWhitespace()
    {
        $program = <<<PROGRAM
<?php
   namespace PhpLint\Test\Ast;
PROGRAM;

        $sourceContext = $this->parser->parse($program, 'test.php');
        $namespaceSourceRange = $sourceContext->getSourceRangeOfNode($sourceContext->getAst()->get('contents')[0]);

        $this->assertEquals('2:4-2:30', $namespaceSourceRange->__toString());

        /** @var AstNode $nameNode */
        $nameNode = $sourceContext->getAst()->get('contents')[0]->get('name');
        $this->assertNodeType(AstNodeType::NAME, $nameNode);
        $this->assertEquals(['PhpLint', 'Test', 'Ast'], $nameNode->get('parts'));
    }

    public function testLocatingAMultilineString()
    {
        $program = <<<PROGRAM
<?php
\$a = 'asdfasdf
asdf
sadffdsa';
PROGRAM;

        $sourceContext = $this->parser->parse($program, 'test.php');
        $expressionNode = $sourceContext->getAst()->get('contents')[0];
        $namespaceSourceRange = $sourceContext->getSourceRangeOfNode($expressionNode);

        $this->assertNodeType(AstNodeType::EXPRESSION, $expressionNode);
        $this->assertEquals('2:1-4:10', $namespaceSourceRange->__toString());
    }
}
