<?php
declare(strict_types=1);

namespace PhpLint\Test\PhpParser;

use PhpLint\AbstractParser;
use PhpLint\Ast\SourceRange;
use PhpLint\PhpParser\PhpParser;
use PhpLint\TestHelpers\AstTestCase;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Expression;

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
        $namespaceSourceRange = $sourceContext->getSourceRangeOfNode($sourceContext->getAst()->contents[0]);

        $this->assertEquals('2:1-2:27', $namespaceSourceRange->__toString());

        /** @var Node $nameNode */
        $nameNode = $sourceContext->getAst()->contents[0]->name;
        $this->assertNodeType(Name::class, $nameNode);
        $this->assertEquals(['PhpLint', 'Test', 'Ast'], $nameNode->parts);
    }

    public function testLocatingANamespaceDeclarationWhichStartsAfterSomeWhitespace()
    {
        $program = <<<PROGRAM
<?php
   namespace PhpLint\Test\Ast;
PROGRAM;

        $sourceContext = $this->parser->parse($program, 'test.php');
        $namespaceSourceRange = $sourceContext->getSourceRangeOfNode($sourceContext->getAst()->contents[0]);

        $this->assertEquals('2:4-2:30', $namespaceSourceRange->__toString());

        /** @var Node $nameNode */
        $nameNode = $sourceContext->getAst()->contents[0]->name;
        $this->assertNodeType(Name::class, $nameNode);
        $this->assertEquals(['PhpLint', 'Test', 'Ast'], $nameNode->parts);
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
        $expressionNode = $sourceContext->getAst()->contents[0];
        $namespaceSourceRange = $sourceContext->getSourceRangeOfNode($expressionNode);

        $this->assertNodeType(Expression::class, $expressionNode);
        $this->assertEquals('2:1-4:10', $namespaceSourceRange->__toString());
    }
}
