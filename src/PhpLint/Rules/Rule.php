<?php
declare(strict_types=1);

namespace PhpLint\Rules;

use PhpLint\Ast\AstNode;
use PhpLint\Linter\LintContext;
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
     * @param LintContext $context
     * @param LintResult $result
     */
    public function validate(AstNode $node, LintContext $context, LintResult $result);
}
