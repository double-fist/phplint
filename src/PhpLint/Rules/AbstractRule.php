<?php
declare(strict_types=1);

namespace PhpLint\Rules;

use PhpLint\Ast\SourceContext;
use PhpLint\Linter\LintResult;
use PhpParser\Node;

abstract class AbstractRule implements Rule
{
    /**
     * @var RuleDescription|null
     */
    protected $description;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->getDescription()->getIdentifier();
    }

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
    public function canValidateNode(Node $node): bool
    {
        return in_array(get_class($node), $this->getTypes());
    }

    /**
     * @inheritdoc
     */
    abstract public function getTypes(): array;

    /**
     * @inheritdoc
     */
    abstract public function validate(Node $node, SourceContext $context, $ruleConfig, LintResult $result);
}
