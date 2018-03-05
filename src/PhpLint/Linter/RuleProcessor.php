<?php
declare(strict_types=1);

namespace PhpLint\Linter;

use PhpLint\Ast\SourceContext;
use PhpLint\Rules\Rule;

class RuleProcessor
{
    /**
     * @var Rule[]
     */
    private $rules;

    /**
     * @param Rule[] $rules
     */
    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * @return Rule[]
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @param SourceContext $sourceContext
     * @param LintResult $result
     */
    public function runRules(SourceContext $sourceContext, LintResult $result)
    {
        // TODO
    }
}
