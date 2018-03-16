<?php
declare(strict_types=1);

namespace PhpLint\Rules;

use PhpLint\Ast\AstNodeTraverser;
use PhpLint\Ast\SourceContext;
use PhpLint\Linter\LintResult;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;

class ClassNamespaceRule extends AbstractRule
{
    const RULE_IDENTIFIER = 'class-namespace';
    const MESSAGE_ID_CLASS_NOT_NAMESPACED = 'classNotNamespaced';

    public function __construct()
    {
        $this->setDescription(
            RuleDescription::forRuleWithIdentifier(self::RULE_IDENTIFIER)
                ->explainedBy('Enforces that all classes must be contained in a PSR-4 namespace.')
                ->usingMessages([
                    self::MESSAGE_ID_CLASS_NOT_NAMESPACED => 'A class must be in a namespace of at least one level.',
                ])
                ->rejectsExamples([
                    RuleDescription::createPhpCodeExample('class AnyClass {}'),
                ])
                ->acceptsExamples([
                    RuleDescription::createPhpCodeExample(
                        'namespace PhpLint\Rules;',
                        'class AnyClass {}'
                    ),
                ])
        );
    }

    /**
     * @inheritdoc
     */
    public function getTypes(): array
    {
        return [
            Class_::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function validate(Node $node, SourceContext $context, $ruleConfig, LintResult $result)
    {
        $parentNode = AstNodeTraverser::getParent($node);
        if (!$parentNode || !($parentNode instanceof Namespace_)) {
            $result->reportViolation(
                $this->getName(),
                RuleSeverity::getRuleSeverity($ruleConfig),
                self::MESSAGE_ID_CLASS_NOT_NAMESPACED,
                $node
            );
        }
    }
}
