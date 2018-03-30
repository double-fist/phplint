<?php
declare(strict_types=1);

namespace PhpLint\Test\Linter\Directive;

use PhpLint\AbstractParser;
use PhpLint\Linter\Directive\DirectiveParser;
use PhpLint\Linter\Directive\DisableDirective;
use PhpLint\PhpParser\PhpParser;
use PHPUnit\Framework\TestCase;

class DirectiveParserTest extends TestCase
{
    /**
     * @var PhpParser|null
     */
    protected $phpParser = null;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->phpParser = AbstractParser::create();
    }

    /**
     * Tests that a 'phplint-disable' directive is not detected, if it's defined in a single line comment '//'.
     */
    public function testSingleLineCommentDisableDirectiveIsNotDetected()
    {
        // Create and parse source context
        $program = <<<PROGRAM
<?php
// phplint-disable
echo 'test';
PROGRAM;
        $sourceContext = $this->phpParser->parse($program, 'test.php');
        $directiveParser = new DirectiveParser($sourceContext);

        // Assert that the disable directive was not detected
        $directives = $directiveParser->getDirectives();
        self::assertCount(0, $directives);
    }

    /**
     * Tests that a 'phplint-disable' directive is detected, if it's defined in a single line block comment '/* *\/'.
     */
    public function testSingleLineBlockCommentDisableDirectiveIsDetected()
    {
        // Create and parse source context
        $program = <<<PROGRAM
<?php
/* phplint-disable */
echo 'test';
PROGRAM;
        $sourceContext = $this->phpParser->parse($program, 'test.php');
        $directiveParser = new DirectiveParser($sourceContext);

        // Assert that the disable directive was detected
        $directives = $directiveParser->getDirectives();
        self::assertCount(1, $directives);
        self::assertEquals('disable', $directives[0]->getType());
        self::assertEquals(2, $directives[0]->getSourceLocation()->getLine());
        self::assertEquals(1, $directives[0]->getSourceLocation()->getColumn());
        self::assertEmpty($directives[0]->getValue());

        // Assert that the disable directive is not unwrapped
        $disableDirectives = $directiveParser->getDisableDirectives();
        self::assertCount(1, $disableDirectives);
        self::assertEquals(DisableDirective::TYPE_DISABLE, $disableDirectives[0]->getType());
        self::assertNull($disableDirectives[0]->getRuleId());

        // Create and parse another source context containing directive configuration
        $program = <<<PROGRAM
<?php
/* phplint-disable ruleA, ruleB -- This is a test, comment. */
echo 'test';
PROGRAM;
        $sourceContext = $this->phpParser->parse($program, 'test.php');
        $directiveParser = new DirectiveParser($sourceContext);

        // Assert that the disable directive was detected
        $directives = $directiveParser->getDirectives();
        self::assertCount(1, $directives);
        self::assertEquals('disable', $directives[0]->getType());
        self::assertEquals(2, $directives[0]->getSourceLocation()->getLine());
        self::assertEquals(1, $directives[0]->getSourceLocation()->getColumn());
        self::assertEquals('ruleA, ruleB', $directives[0]->getValue());
    }

    /**
     * Tests that a 'phplint-disable' directive is detected, if it's defined in a multi line block comment '/* *\/'.
     */
    public function testMultiLineLineBlockCommentDisableDirectiveIsDetected()
    {
        // Create and parse source context
        $program = <<<PROGRAM
<?php
/*
 phplint-disable
 */
echo 'test';
PROGRAM;
        $sourceContext = $this->phpParser->parse($program, 'test.php');
        $directiveParser = new DirectiveParser($sourceContext);

        // Assert that the disable directive was detected
        $directives = $directiveParser->getDirectives();
        self::assertCount(1, $directives);
        self::assertEquals('disable', $directives[0]->getType());
        self::assertEquals(2, $directives[0]->getSourceLocation()->getLine());
        self::assertEquals(1, $directives[0]->getSourceLocation()->getColumn());

        // Assert that the disable directive is not unwrapped
        $disableDirectives = $directiveParser->getDisableDirectives();
        self::assertCount(1, $disableDirectives);
        self::assertEquals(DisableDirective::TYPE_DISABLE, $disableDirectives[0]->getType());
        self::assertNull($disableDirectives[0]->getRuleId());

        // Create and parse another source context containing directive configuration
        $program = <<<PROGRAM
<?php
/*
 phplint-disable ruleA, ruleB -- This is a test, comment.
*/
echo 'test';
PROGRAM;
        $sourceContext = $this->phpParser->parse($program, 'test.php');
        $directiveParser = new DirectiveParser($sourceContext);

        // Assert that the disable directive was detected
        $directives = $directiveParser->getDirectives();
        self::assertCount(1, $directives);
        self::assertEquals('disable', $directives[0]->getType());
        self::assertEquals(2, $directives[0]->getSourceLocation()->getLine());
        self::assertEquals(1, $directives[0]->getSourceLocation()->getColumn());
        self::assertEquals('ruleA, ruleB', $directives[0]->getValue());
    }

    /**
     * Tests that a 'phplint-enable' directive is not detected, if it's defined in a single line comment '//'.
     */
    public function testSingleLineCommentEnableDirectiveIsNotDetected()
    {
        // Create and parse source context
        $program = <<<PROGRAM
<?php
// phplint-enable
echo 'test';
PROGRAM;
        $sourceContext = $this->phpParser->parse($program, 'test.php');
        $directiveParser = new DirectiveParser($sourceContext);

        // Assert that the enable directive was not detected
        $directives = $directiveParser->getDirectives();
        self::assertCount(0, $directives);
    }

    /**
     * Tests that a 'phplint-enable' directive is detected, if it's defined in a single line block comment '/* *\/'.
     */
    public function testSingleLineBlockCommentEnableDirectiveIsDetected()
    {
        // Create and parse source context
        $program = <<<PROGRAM
<?php
/* phplint-enable */
echo 'test';
PROGRAM;
        $sourceContext = $this->phpParser->parse($program, 'test.php');
        $directiveParser = new DirectiveParser($sourceContext);

        // Assert that the enable directive was detected
        $directives = $directiveParser->getDirectives();
        self::assertCount(1, $directives);
        self::assertEquals('enable', $directives[0]->getType());
        self::assertEquals(2, $directives[0]->getSourceLocation()->getLine());
        self::assertEquals(1, $directives[0]->getSourceLocation()->getColumn());

        // Assert that the enable directive is not unwrapped
        $disableDirectives = $directiveParser->getDisableDirectives();
        self::assertCount(1, $disableDirectives);
        self::assertEquals(DisableDirective::TYPE_ENABLE, $disableDirectives[0]->getType());
        self::assertNull($disableDirectives[0]->getRuleId());

        // Create and parse another source context containing directive configuration
        $program = <<<PROGRAM
<?php
/* phplint-enable ruleA, ruleB -- This is a test, comment. */
echo 'test';
PROGRAM;
        $sourceContext = $this->phpParser->parse($program, 'test.php');
        $directiveParser = new DirectiveParser($sourceContext);

        // Assert that the enable directive was detected
        $directives = $directiveParser->getDirectives();
        self::assertCount(1, $directives);
        self::assertEquals('enable', $directives[0]->getType());
        self::assertEquals(2, $directives[0]->getSourceLocation()->getLine());
        self::assertEquals(1, $directives[0]->getSourceLocation()->getColumn());
        self::assertEquals('ruleA, ruleB', $directives[0]->getValue());
    }

    /**
     * Tests that a 'phplint-disable' directive is detected, if it's defined in a multi line block comment '/* *\/'.
     */
    public function testMultiLineLineBlockCommentEnableDirectiveIsDetected()
    {
        // Create and parse source context
        $program = <<<PROGRAM
<?php
/*
 phplint-enable
 */
echo 'test';
PROGRAM;
        $sourceContext = $this->phpParser->parse($program, 'test.php');
        $directiveParser = new DirectiveParser($sourceContext);

        // Assert that the enable directive was detected
        $directives = $directiveParser->getDirectives();
        self::assertCount(1, $directives);
        self::assertEquals('enable', $directives[0]->getType());
        self::assertEquals(2, $directives[0]->getSourceLocation()->getLine());
        self::assertEquals(1, $directives[0]->getSourceLocation()->getColumn());

        // Assert that the enable directive is not unwrapped
        $disableDirectives = $directiveParser->getDisableDirectives();
        self::assertCount(1, $disableDirectives);
        self::assertEquals(DisableDirective::TYPE_ENABLE, $disableDirectives[0]->getType());
        self::assertNull($disableDirectives[0]->getRuleId());

        // Create and parse another source context containing directive configuration
        $program = <<<PROGRAM
<?php
/*
 phplint-enable ruleA, ruleB -- This is a test, comment.
 */
echo 'test';
PROGRAM;
        $sourceContext = $this->phpParser->parse($program, 'test.php');
        $directiveParser = new DirectiveParser($sourceContext);

        // Assert that the enable directive was detected
        $directives = $directiveParser->getDirectives();
        self::assertCount(1, $directives);
        self::assertEquals('enable', $directives[0]->getType());
        self::assertEquals(2, $directives[0]->getSourceLocation()->getLine());
        self::assertEquals(1, $directives[0]->getSourceLocation()->getColumn());
        self::assertEquals('ruleA, ruleB', $directives[0]->getValue());
    }

    /**
     * Tests that a 'phplint-disable-line' directive is detected, if it's defined in a single line comment '//'.
     */
    public function testSingleLineCommentDisableLineDirectiveIsDetected()
    {
        // Create and parse source context
        $program = <<<PROGRAM
<?php
echo 'test'; // phplint-disable-line
PROGRAM;
        $sourceContext = $this->phpParser->parse($program, 'test.php');
        $directiveParser = new DirectiveParser($sourceContext);

        // Assert that the disable-line directive was detected
        $directives = $directiveParser->getDirectives();
        self::assertCount(1, $directives);
        self::assertEquals('disable-line', $directives[0]->getType());
        self::assertEquals(2, $directives[0]->getSourceLocation()->getLine());
        self::assertEquals(14, $directives[0]->getSourceLocation()->getColumn());

        // Assert the disable-line directive is unwrapped into a disable and an enable directive
        $disableDirectives = $directiveParser->getDisableDirectives();
        self::assertCount(2, $disableDirectives);
        // Disable from the beginning of the line...
        self::assertEquals(DisableDirective::TYPE_DISABLE, $disableDirectives[0]->getType());
        self::assertEquals(2, $disableDirectives[0]->getSourceLocation()->getLine());
        self::assertEquals(1, $disableDirectives[0]->getSourceLocation()->getColumn());
        self::assertNull($disableDirectives[0]->getRuleId());
        // ... till just before the beginning of the next line
        self::assertEquals(DisableDirective::TYPE_ENABLE, $disableDirectives[1]->getType());
        self::assertEquals(3, $disableDirectives[1]->getSourceLocation()->getLine());
        self::assertEquals(0, $disableDirectives[1]->getSourceLocation()->getColumn());
        self::assertNull($disableDirectives[1]->getRuleId());

        // Create and parse another source context containing directive configuration
        $program = <<<PROGRAM
<?php
echo 'test'; // phplint-disable-line ruleA, ruleB -- This is a test, comment.
PROGRAM;
        $sourceContext = $this->phpParser->parse($program, 'test.php');
        $directiveParser = new DirectiveParser($sourceContext);

        // Assert that the disable-line directive was detected
        $directives = $directiveParser->getDirectives();
        self::assertCount(1, $directives);
        self::assertEquals('disable-line', $directives[0]->getType());
        self::assertEquals(2, $directives[0]->getSourceLocation()->getLine());
        self::assertEquals(14, $directives[0]->getSourceLocation()->getColumn());
        self::assertEquals('ruleA, ruleB', $directives[0]->getValue());

        // Assert the disable-line directive is split and unwrapped into a disable and an enable directive for each
        // ruleId it disables
        $disableDirectives = $directiveParser->getDisableDirectives();
        self::assertCount(4, $disableDirectives);
        // Disable ruleA from the beginning of the line...
        self::assertEquals(DisableDirective::TYPE_DISABLE, $disableDirectives[0]->getType());
        self::assertEquals(2, $disableDirectives[0]->getSourceLocation()->getLine());
        self::assertEquals(1, $disableDirectives[0]->getSourceLocation()->getColumn());
        self::assertEquals('ruleA', $disableDirectives[0]->getRuleId());
        // ... till just before the beginning of the next line
        self::assertEquals(DisableDirective::TYPE_ENABLE, $disableDirectives[2]->getType());
        self::assertEquals(3, $disableDirectives[2]->getSourceLocation()->getLine());
        self::assertEquals(0, $disableDirectives[2]->getSourceLocation()->getColumn());
        self::assertEquals('ruleA', $disableDirectives[2]->getRuleId());
        // Disable ruleB from the beginning of the line...
        self::assertEquals(DisableDirective::TYPE_DISABLE, $disableDirectives[1]->getType());
        self::assertEquals(2, $disableDirectives[1]->getSourceLocation()->getLine());
        self::assertEquals(1, $disableDirectives[1]->getSourceLocation()->getColumn());
        self::assertEquals('ruleB', $disableDirectives[1]->getRuleId());
        // ... till just before the beginning of the next line
        self::assertEquals(DisableDirective::TYPE_ENABLE, $disableDirectives[3]->getType());
        self::assertEquals(3, $disableDirectives[3]->getSourceLocation()->getLine());
        self::assertEquals(0, $disableDirectives[3]->getSourceLocation()->getColumn());
        self::assertEquals('ruleB', $disableDirectives[3]->getRuleId());
    }

    /**
     * Tests that a 'phplint-disable-line' directive is detected, if it's defined in a single line block
     * comment '/* *\/'.
     */
    public function testSingleLineBlockCommentDisableLineDirectiveIsDetected()
    {
        // Create and parse source context
        $program = <<<PROGRAM
<?php
echo 'test'; /* phplint-disable-line */
PROGRAM;
        $sourceContext = $this->phpParser->parse($program, 'test.php');
        $directiveParser = new DirectiveParser($sourceContext);

        // Assert that the disable-line directive was detected
        $directives = $directiveParser->getDirectives();
        self::assertCount(1, $directives);
        self::assertEquals('disable-line', $directives[0]->getType());
        self::assertEquals(2, $directives[0]->getSourceLocation()->getLine());
        self::assertEquals(14, $directives[0]->getSourceLocation()->getColumn());

        // Assert the disable-line directive is unwrapped into a disable and an enable directive
        $disableDirectives = $directiveParser->getDisableDirectives();
        self::assertCount(2, $disableDirectives);
        // Disable from the beginning of the line...
        self::assertEquals(DisableDirective::TYPE_DISABLE, $disableDirectives[0]->getType());
        self::assertEquals(2, $disableDirectives[0]->getSourceLocation()->getLine());
        self::assertEquals(1, $disableDirectives[0]->getSourceLocation()->getColumn());
        self::assertNull($disableDirectives[0]->getRuleId());
        // ... till just before the beginning of the next line
        self::assertEquals(DisableDirective::TYPE_ENABLE, $disableDirectives[1]->getType());
        self::assertEquals(3, $disableDirectives[1]->getSourceLocation()->getLine());
        self::assertEquals(0, $disableDirectives[1]->getSourceLocation()->getColumn());
        self::assertNull($disableDirectives[1]->getRuleId());

        // Create and parse another source context containing directive configuration
        $program = <<<PROGRAM
<?php
echo 'test'; /* phplint-disable-line ruleA, ruleB -- This is a test, comment. */
PROGRAM;
        $sourceContext = $this->phpParser->parse($program, 'test.php');
        $directiveParser = new DirectiveParser($sourceContext);

        // Assert that the disable-line directive was detected
        $directives = $directiveParser->getDirectives();
        self::assertCount(1, $directives);
        self::assertEquals('disable-line', $directives[0]->getType());
        self::assertEquals(2, $directives[0]->getSourceLocation()->getLine());
        self::assertEquals(14, $directives[0]->getSourceLocation()->getColumn());
        self::assertEquals('ruleA, ruleB', $directives[0]->getValue());

        // Assert the disable-line directive is split and unwrapped into a disable and an enable directive for each
        // ruleId it disables
        $disableDirectives = $directiveParser->getDisableDirectives();
        self::assertCount(4, $disableDirectives);
        // Disable ruleA from the beginning of the line...
        self::assertEquals(DisableDirective::TYPE_DISABLE, $disableDirectives[0]->getType());
        self::assertEquals(2, $disableDirectives[0]->getSourceLocation()->getLine());
        self::assertEquals(1, $disableDirectives[0]->getSourceLocation()->getColumn());
        self::assertEquals('ruleA', $disableDirectives[0]->getRuleId());
        // ... till just before the beginning of the next line
        self::assertEquals(DisableDirective::TYPE_ENABLE, $disableDirectives[2]->getType());
        self::assertEquals(3, $disableDirectives[2]->getSourceLocation()->getLine());
        self::assertEquals(0, $disableDirectives[2]->getSourceLocation()->getColumn());
        self::assertEquals('ruleA', $disableDirectives[2]->getRuleId());
        // Disable ruleB from the beginning of the line...
        self::assertEquals(DisableDirective::TYPE_DISABLE, $disableDirectives[1]->getType());
        self::assertEquals(2, $disableDirectives[1]->getSourceLocation()->getLine());
        self::assertEquals(1, $disableDirectives[1]->getSourceLocation()->getColumn());
        self::assertEquals('ruleB', $disableDirectives[1]->getRuleId());
        // ... till just before the beginning of the next line
        self::assertEquals(DisableDirective::TYPE_ENABLE, $disableDirectives[3]->getType());
        self::assertEquals(3, $disableDirectives[3]->getSourceLocation()->getLine());
        self::assertEquals(0, $disableDirectives[3]->getSourceLocation()->getColumn());
        self::assertEquals('ruleB', $disableDirectives[3]->getRuleId());
    }

    /**
     * Tests that a 'phplint-disable-line' directive is not detected, if it's defined in a multi line block
     * comment '/* *\/'.
     */
    public function testMultiLineBlockCommentDisableLineDirectiveIsNotDetected()
    {
        // Create and parse source context
        $program = <<<PROGRAM
<?php
echo 'test'; /*
 phplint-disable-line
*/
PROGRAM;
        $sourceContext = $this->phpParser->parse($program, 'test.php');
        $directiveParser = new DirectiveParser($sourceContext);

        // Assert that the disable-line directive was not detected
        $directives = $directiveParser->getDirectives();
        self::assertCount(0, $directives);
    }

    /**
     * Tests that a 'phplint-disable-next-line' directive is detected, if it's defined in a single line comment '//'.
     */
    public function testSingleLineCommentDisableNextLineDirectiveIsDetected()
    {
        // Create and parse source context
        $program = <<<PROGRAM
<?php
// phplint-disable-next-line
echo 'test';
PROGRAM;
        $sourceContext = $this->phpParser->parse($program, 'test.php');
        $directiveParser = new DirectiveParser($sourceContext);

        // Assert that the disable-next-line directive was detected
        $directives = $directiveParser->getDirectives();
        self::assertCount(1, $directives);
        self::assertEquals('disable-next-line', $directives[0]->getType());
        self::assertEquals(2, $directives[0]->getSourceLocation()->getLine());
        self::assertEquals(1, $directives[0]->getSourceLocation()->getColumn());

        // Assert the disable-next-line directive is unwrapped into a disable and an enable directive
        $disableDirectives = $directiveParser->getDisableDirectives();
        self::assertCount(2, $disableDirectives);
        // Disable from the beginning of the next line...
        self::assertEquals(DisableDirective::TYPE_DISABLE, $disableDirectives[0]->getType());
        self::assertEquals(3, $disableDirectives[0]->getSourceLocation()->getLine());
        self::assertEquals(1, $disableDirectives[0]->getSourceLocation()->getColumn());
        self::assertNull($disableDirectives[0]->getRuleId());
        // ... till just before the beginning of the next but one line
        self::assertEquals(DisableDirective::TYPE_ENABLE, $disableDirectives[1]->getType());
        self::assertEquals(4, $disableDirectives[1]->getSourceLocation()->getLine());
        self::assertEquals(0, $disableDirectives[1]->getSourceLocation()->getColumn());
        self::assertNull($disableDirectives[1]->getRuleId());

        // Create and parse another source context containing directive configuration
        $program = <<<PROGRAM
<?php
// phplint-disable-next-line ruleA, ruleB -- This is a test, comment.
echo 'test';
PROGRAM;
        $sourceContext = $this->phpParser->parse($program, 'test.php');
        $directiveParser = new DirectiveParser($sourceContext);

        // Assert that the disable-next-line directive was detected
        $directives = $directiveParser->getDirectives();
        self::assertCount(1, $directives);
        self::assertEquals('disable-next-line', $directives[0]->getType());
        self::assertEquals(2, $directives[0]->getSourceLocation()->getLine());
        self::assertEquals(1, $directives[0]->getSourceLocation()->getColumn());
        self::assertEquals('ruleA, ruleB', $directives[0]->getValue());

        // Assert the disable-line directive is split and unwrapped into a disable and an enable directive for each
        // ruleId it disables
        $disableDirectives = $directiveParser->getDisableDirectives();
        self::assertCount(4, $disableDirectives);
        // Disable ruleA from the beginning of the next line...
        self::assertEquals(DisableDirective::TYPE_DISABLE, $disableDirectives[0]->getType());
        self::assertEquals(3, $disableDirectives[0]->getSourceLocation()->getLine());
        self::assertEquals(1, $disableDirectives[0]->getSourceLocation()->getColumn());
        self::assertEquals('ruleA', $disableDirectives[0]->getRuleId());
        // ... till just before the beginning of the next but one line
        self::assertEquals(DisableDirective::TYPE_ENABLE, $disableDirectives[2]->getType());
        self::assertEquals(4, $disableDirectives[2]->getSourceLocation()->getLine());
        self::assertEquals(0, $disableDirectives[2]->getSourceLocation()->getColumn());
        self::assertEquals('ruleA', $disableDirectives[2]->getRuleId());
        // Disable ruleB from the beginning of the next line...
        self::assertEquals(DisableDirective::TYPE_DISABLE, $disableDirectives[1]->getType());
        self::assertEquals(3, $disableDirectives[1]->getSourceLocation()->getLine());
        self::assertEquals(1, $disableDirectives[1]->getSourceLocation()->getColumn());
        self::assertEquals('ruleB', $disableDirectives[1]->getRuleId());
        // ... till just before the beginning of the next but one line
        self::assertEquals(DisableDirective::TYPE_ENABLE, $disableDirectives[3]->getType());
        self::assertEquals(4, $disableDirectives[3]->getSourceLocation()->getLine());
        self::assertEquals(0, $disableDirectives[3]->getSourceLocation()->getColumn());
        self::assertEquals('ruleB', $disableDirectives[3]->getRuleId());
    }

    /**
     * Tests that a 'phplint-disable-next-line' directive is detected, if it's defined in a single line block
     * comment '/* *\/'.
     */
    public function testSingleLineBlockCommentDisableNextLineDirectiveIsDetected()
    {
        // Create and parse source context
        $program = <<<PROGRAM
<?php
/* phplint-disable-next-line */
echo 'test';
PROGRAM;
        $sourceContext = $this->phpParser->parse($program, 'test.php');
        $directiveParser = new DirectiveParser($sourceContext);

        // Assert that the disable-next-line directive was detected
        $directives = $directiveParser->getDirectives();
        self::assertCount(1, $directives);
        self::assertEquals('disable-next-line', $directives[0]->getType());
        self::assertEquals(2, $directives[0]->getSourceLocation()->getLine());
        self::assertEquals(1, $directives[0]->getSourceLocation()->getColumn());

        // Assert the disable-next-line directive is unwrapped into a disable and an enable directive
        $disableDirectives = $directiveParser->getDisableDirectives();
        self::assertCount(2, $disableDirectives);
        // Disable from the beginning of the next line...
        self::assertEquals(DisableDirective::TYPE_DISABLE, $disableDirectives[0]->getType());
        self::assertEquals(3, $disableDirectives[0]->getSourceLocation()->getLine());
        self::assertEquals(1, $disableDirectives[0]->getSourceLocation()->getColumn());
        self::assertNull($disableDirectives[0]->getRuleId());
        // ... till just before the beginning of the next but one line
        self::assertEquals(DisableDirective::TYPE_ENABLE, $disableDirectives[1]->getType());
        self::assertEquals(4, $disableDirectives[1]->getSourceLocation()->getLine());
        self::assertEquals(0, $disableDirectives[1]->getSourceLocation()->getColumn());
        self::assertNull($disableDirectives[1]->getRuleId());

        // Create and parse another source context containing directive configuration
        $program = <<<PROGRAM
<?php
/* phplint-disable-next-line ruleA, ruleB -- This is a test, comment. */
echo 'test';
PROGRAM;
        $sourceContext = $this->phpParser->parse($program, 'test.php');
        $directiveParser = new DirectiveParser($sourceContext);

        // Assert that the disable-next-line directive was detected
        $directives = $directiveParser->getDirectives();
        self::assertCount(1, $directives);
        self::assertEquals('disable-next-line', $directives[0]->getType());
        self::assertEquals(2, $directives[0]->getSourceLocation()->getLine());
        self::assertEquals(1, $directives[0]->getSourceLocation()->getColumn());
        self::assertEquals('ruleA, ruleB', $directives[0]->getValue());

        // Assert the disable-line directive is split and unwrapped into a disable and an enable directive for each
        // ruleId it disables
        $disableDirectives = $directiveParser->getDisableDirectives();
        self::assertCount(4, $disableDirectives);
        // Disable ruleA from the beginning of the next line...
        self::assertEquals(DisableDirective::TYPE_DISABLE, $disableDirectives[0]->getType());
        self::assertEquals(3, $disableDirectives[0]->getSourceLocation()->getLine());
        self::assertEquals(1, $disableDirectives[0]->getSourceLocation()->getColumn());
        self::assertEquals('ruleA', $disableDirectives[0]->getRuleId());
        // ... till just before the beginning of the next but one line
        self::assertEquals(DisableDirective::TYPE_ENABLE, $disableDirectives[2]->getType());
        self::assertEquals(4, $disableDirectives[2]->getSourceLocation()->getLine());
        self::assertEquals(0, $disableDirectives[2]->getSourceLocation()->getColumn());
        self::assertEquals('ruleA', $disableDirectives[2]->getRuleId());
        // Disable ruleB from the beginning of the next line...
        self::assertEquals(DisableDirective::TYPE_DISABLE, $disableDirectives[1]->getType());
        self::assertEquals(3, $disableDirectives[1]->getSourceLocation()->getLine());
        self::assertEquals(1, $disableDirectives[1]->getSourceLocation()->getColumn());
        self::assertEquals('ruleB', $disableDirectives[1]->getRuleId());
        // ... till just before the beginning of the next but one line
        self::assertEquals(DisableDirective::TYPE_ENABLE, $disableDirectives[3]->getType());
        self::assertEquals(4, $disableDirectives[3]->getSourceLocation()->getLine());
        self::assertEquals(0, $disableDirectives[3]->getSourceLocation()->getColumn());
        self::assertEquals('ruleB', $disableDirectives[3]->getRuleId());
    }

    /**
     * Tests that a 'phplint-disable-next-line' directive is not detected, if it's defined in a multi line block
     * comment '/* *\/'.
     */
    public function testMultiLineBlockCommentDisableNextLineDirectiveIsNotDetected()
    {
        // Create and parse source context
        $program = <<<PROGRAM
<?php
/*
 phplint-disable-next-line
 */
echo 'test';
PROGRAM;
        $sourceContext = $this->phpParser->parse($program, 'test.php');
        $directiveParser = new DirectiveParser($sourceContext);

        // Assert that the disable-next-line directive was not detected
        $directives = $directiveParser->getDirectives();
        self::assertCount(0, $directives);
    }
}
