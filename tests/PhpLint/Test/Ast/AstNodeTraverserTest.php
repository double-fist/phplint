<?php
declare(strict_types=1);

namespace PhpLint\Test\Ast;

use PhpLint\Ast\AstNodeTraverser;
use PHPUnit\Framework\TestCase;

class AstNodeTraverserTest extends TestCase
{
    public function testTraversalIsDepthFirst()
    {
        $testTree = TestAstNode::createFromArrayDescription(
            'root',
            [
                'root_child0' => [
                    'root_child0_child0',
                    'root_child0_child1',
                ],
                'root_child1' => [
                    'root_child1_child0',
                ],
            ]
        );
        $traverser = new AstNodeTraverser($testTree);

        self::assertEqualsNode($testTree, $traverser->current());
        self::assertTrue($traverser->valid());
        $traverser->next();

        self::assertEqualsNode($testTree->getChildren()[0], $traverser->current());
        self::assertTrue($traverser->valid());
        $traverser->next();

        self::assertEqualsNode($testTree->getChildren()[0]->getChildren()[0], $traverser->current());
        self::assertTrue($traverser->valid());
        $traverser->next();

        self::assertEqualsNode($testTree->getChildren()[0]->getChildren()[1], $traverser->current());
        self::assertTrue($traverser->valid());
        $traverser->next();

        self::assertEqualsNode($testTree->getChildren()[1], $traverser->current());
        self::assertTrue($traverser->valid());
        $traverser->next();

        self::assertEqualsNode($testTree->getChildren()[1]->getChildren()[0], $traverser->current());
        self::assertTrue($traverser->valid());
        $traverser->next();

        // Reached the end of the tree
        self::assertFalse($traverser->valid());
    }

    public function testIsIterable()
    {
        $testTree = TestAstNode::createFromArrayDescription(
            'root',
            [
                'root_child0' => [
                    'root_child0_child0',
                    'root_child0_child1',
                ],
                'root_child1' => [
                    'root_child1_child0',
                ],
            ]
        );
        $traverser = new AstNodeTraverser($testTree);

        $expectedNodes = [
            $testTree,
            $testTree->getChildren()[0],
            $testTree->getChildren()[0]->getChildren()[0],
            $testTree->getChildren()[0]->getChildren()[1],
            $testTree->getChildren()[1],
            $testTree->getChildren()[1]->getChildren()[0],
        ];
        $interatorCounter = 0;
        foreach ($traverser as $nodeIndex => $node) {
            self::assertEquals($interatorCounter, $nodeIndex);
            $interatorCounter += 1;
            $nextExpectedNode = array_shift($expectedNodes);
            self::assertEqualsNode($nextExpectedNode, $node);
        }
        self::assertEquals(6, $interatorCounter);
        self::assertEmpty($expectedNodes);
    }

    /**
     * @param TestAstNode $expectedNode
     * @param TestAstNode $actualNode
     */
    protected static function assertEqualsNode(TestAstNode $expectedNode, TestAstNode $actualNode)
    {
        self::assertTrue(
            $actualNode->equals($expectedNode),
            sprintf(
                'Failed asserting that actual node "%s" equals expected node "%s".',
                $actualNode->getId(),
                $expectedNode->getId()
            )
        );
    }
}
