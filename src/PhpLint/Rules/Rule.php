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
     * @param string
     */
    public function getSeverity(): string;

    /**
     * @param string|array $config
     */
    public function configure($config);

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
     * @param LintResult $result
     */
    public function validate(Node $node, SourceContext $context, LintResult $result);
}
