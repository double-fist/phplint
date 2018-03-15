<?php
declare(strict_types=1);

namespace PhpLint\Rules;

use PhpLint\Ast\AstNode;
use PhpLint\Ast\SourceContext;
use PhpLint\Linter\LintResult;

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
     * @param AstNode $node
     * @return bool
     */
    public function canValidateNode(AstNode $node): bool;

    /**
     * @param AstNode $node
     * @param SourceContext $context
     * @param string|array $ruleConfig
     * @param LintResult $result
     */
    public function validate(AstNode $node, SourceContext $context, $ruleConfig, LintResult $result);
}
