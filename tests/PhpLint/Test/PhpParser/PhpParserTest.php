<?php
declare(strict_types=1);

namespace PhpLint\Test\PhpParser;

use PhpLint\AbstractParser;
use PhpLint\Ast\Node\SourceRoot;
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
        $this->assertNodeType(SourceRoot::class, $sourceRoot);
    }
}
