<?php
declare(strict_types=1);

namespace PhpLint\Rules;

use PhpLint\Ast\SourceContext;
use PhpLint\Linter\LintResult;
use PhpParser\Node;

interface Rule
{
    /**
     * @return RuleDescription
     */
    public function getDescription(): RuleDescription;

    /**
     * @param RuleDescription $description
     */
    public function setDescription(RuleDescription $description);

    /**
     * @return string[]
     */
    public function getTypes(): array;

    /**
     * @param Node $node
     * @return bool
     */
    public function canValidateNode(Node $node): bool;

    /**
     * @param Node $node
     * @param SourceContext $context
     * @param string|array $ruleConfig
     * @param LintResult $result
     */
    public function validate(Node $node, SourceContext $context, $ruleConfig, LintResult $result);
}
