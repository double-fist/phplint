<?php
declare(strict_types=1);

namespace PhpLint\Test\Rules;

use PhpLint\Rules\RuleDescription;
use PHPUnit\Framework\TestCase;

class RuleDescriptionTest extends TestCase
{
    public function testBuilderCreatesValidDescription()
    {
        $identifier = 'a-rule-identifier';
        $ruleDescription = RuleDescription::forRuleWithIdentifier($identifier);
        self::assertNotNull($ruleDescription);
        self::assertEquals($identifier, $ruleDescription->getIdentifier());

        $explanation = 'Some text explaining the rule';
        $updatedRuleDescription = $ruleDescription->explainedBy($explanation);
        self::assertTrue($updatedRuleDescription === $ruleDescription);
        self::assertEquals($explanation, $ruleDescription->getExplanation());

        // TODO: Assert building with configuration schema

        $messageIds = [
            'messageId A',
            'messageId B',
        ];
        $updatedRuleDescription = $ruleDescription->usingMessageIds($messageIds);
        self::assertTrue($updatedRuleDescription === $ruleDescription);
        self::assertEquals($messageIds, $ruleDescription->getMessageIds());

        $rejectedExamples = [
            'rejected example A',
            'rejected example B',
        ];
        $updatedRuleDescription = $ruleDescription->rejectsExamples($rejectedExamples);
        self::assertTrue($updatedRuleDescription === $ruleDescription);
        self::assertEquals($rejectedExamples, $ruleDescription->getRejectedExamples());

        $acceptedExamples = [
            'accepted example A',
            'accepted example B',
        ];
        $updatedRuleDescription = $ruleDescription->acceptsExamples($acceptedExamples);
        self::assertTrue($updatedRuleDescription === $ruleDescription);
        self::assertEquals($acceptedExamples, $ruleDescription->getAcceptedExamples());

        $fixableExamples = [
            'accepted example A',
            'accepted example B',
        ];
        $updatedRuleDescription = $ruleDescription->fixesExamples($fixableExamples);
        self::assertTrue($updatedRuleDescription === $ruleDescription);
        self::assertEquals($fixableExamples, $ruleDescription->getFixableExamples());
    }
}
