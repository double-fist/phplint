<?php
declare(strict_types=1);

namespace PhpLint\Linter;

use PhpLint\Ast\AstNode;
use PhpLint\Ast\SourceContext;
use PhpLint\Configuration\Configuration;
use PhpLint\Rules\RuleLoader;

class RuleProcessor
{
    /**
     * @var Configuration
     */
    private $config;

    /**
     * @var RuleLoader
     */
    private $ruleLoader;

    /**
     * @param Configuration $config
     */
    public function __construct(Configuration $config)
    {
        $this->config = $config;
        $this->ruleLoader = new RuleLoader($config);
    }

    /**
     * @param AstNode $node
     * @param SourceContext $sourceContext
     * @param LintResult $lintResult
     */
    public function runRules(AstNode $node, SourceContext $sourceContext, LintResult $lintResult)
    {
        foreach ($this->config->getRules() as $ruleName => $ruleConfig) {
            $rule = $this->ruleLoader->loadRule($ruleName);
            if ($rule->canValidateNode($node)) {
                $rule->validate($node, $sourceContext, $ruleConfig, $lintResult);
            }
        }
    }
}
