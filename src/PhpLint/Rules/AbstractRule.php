<?php
declare(strict_types=1);

namespace PhpLint\Rules;

use PhpLint\Ast\AstNode;
use PhpLint\Ast\SourceContext;
use PhpLint\Linter\LintResult;

abstract class AbstractRule implements Rule
{
    /**
     * @var RuleDescription|null
     */
    protected $description;

    /**
     * @inheritdoc
     */
    public function getDescription(): RuleDescription
    {
        return $this->description;
    }

    /**
     * @inheritdoc
     */
    public function setDescription(RuleDescription $description)
    {
        $this->description = $description;
    }

    /**
     * @inheritdoc
     */
    public function canValidateNode(AstNode $node): bool
    {
        return in_array($node->getType(), $this->getTypes());
    }

    /**
     * @inheritdoc
     */
    abstract public function getTypes(): array;

    /**
     * @inheritdoc
     */
    abstract public function validate(AstNode $node, SourceContext $context, $ruleConfig, LintResult $result);
}
