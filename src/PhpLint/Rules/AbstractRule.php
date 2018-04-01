<?php
declare(strict_types=1);

namespace PhpLint\Rules;

use PhpLint\Ast\SourceContext;
use PhpLint\Linter\LintResult;
use PhpLint\Rules\RuleSeverity;
use PhpParser\Node;

abstract class AbstractRule implements Rule
{
    /**
     * @var RuleDescription|null
     */
    protected $description;

    /**
     * @var string
     */
    protected $severity;

    /**
     * @param string $severity (optional, defaults to 'error')
     */
    public function __construct(string $severity = RuleSeverity::SEVERITY_ERROR)
    {
        $this->setSeverity($severity);
    }

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
    public function getSeverity(): string
    {
        return $this->severity;
    }

    /**
     * @param mixed $severity
     * @throws RuleException if the passed $severity is invalid.
     */
    protected function setSeverity($severity)
    {
        if (!RuleSeverity::isRuleSeverity($severity)) {
            throw RuleException::invalidSeverity($severity);
        }

        // Make sure to use the string version of the severity
        $this->severity = RuleSeverity::getRuleSeverity($severity, true);
    }

    /**
     * @inheritdoc
     */
    public function configure($config)
    {
        $this->setSeverity(RuleSeverity::getRuleSeverity($config, true));
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
    abstract public function validate(Node $node, SourceContext $context, LintResult $result);
}
