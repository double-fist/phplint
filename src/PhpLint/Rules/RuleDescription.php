<?php
declare(strict_types=1);

namespace PhpLint\Rules;

class RuleDescription
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string|null
     */
    protected $explanation = null;

    /**
     * @var RuleConfigurationSchema|null
     */
    protected $schema = null;

    /**
     * @var string[]
     */
    protected $rejectedExamples = [];

    /**
     * @var string[]
     */
    protected $acceptedExamples = [];

    /**
     * @var array
     */
    protected $fixableExamples = [];

    /**
     * @param string $identifier
     * @return RuleDescription
     */
    public static function forRuleWithIdentifier(string $identifier): RuleDescription
    {
        return new self($identifier);
    }

    /**
     * @param string $identifier
     */
    protected function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @param string $explanation
     * @return RuleDescription
     */
    public function explainedBy(string $explanation): RuleDescription
    {
        $this->explanation = $explanation;

        return $this;
    }

    /**
     * @param RuleConfigurationSchema $schema
     * @return RuleDescription
     */
    public function configurableFollowingSchema(RuleConfigurationSchema $schema): RuleDescription
    {
        $this->schema = $schema;

        return $this;
    }

    /**
     * @param string[] $rejectedExamples
     * @return RuleDescription
     */
    public function rejectsExamples(array $rejectedExamples): RuleDescription
    {
        $this->rejectedExamples = $rejectedExamples;

        return $this;
    }

    /**
     * @param string[] $acceptedExamples
     * @return RuleDescription
     */
    public function acceptsExamples(array $acceptedExamples): RuleDescription
    {
        $this->acceptedExamples = $acceptedExamples;

        return $this;
    }

    /**
     * @param [] $fixableExamples
     * @return RuleDescription
     */
    public function fixesExamples(array $fixableExamples): RuleDescription
    {
        $this->fixableExamples = $fixableExamples;

        return $this;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return string|null
     */
    public function getExplanation()
    {
        return $this->explanation;
    }

    /**
     * @return RuleConfigurationSchema|null
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @return string[]
     */
    public function getRejectedExamples(): array
    {
        return $this->rejectedExamples;
    }

    /**
     * @return string[]
     */
    public function getAcceptedExamples(): array
    {
        return $this->acceptedExamples;
    }

    /**
     * @return array
     */
    public function getFixableExamples(): array
    {
        return $this->fixableExamples;
    }

    /**
     * @param string ...$lines
     * @return string
     */
    public static function createPlainCodeExample(string ...$lines): string
    {
        return implode("\n", $lines);
    }

    /**
     * @param string ...$lines
     * @return string
     */
    public static function createPhpCodeExample(string ...$lines): string
    {
        $code = self::createPlainCodeExample(...$lines);
        if (mb_strpos($code, '<?') !== 0) {
            $code = "<?php\n" . $code;
        }

        return $code;
    }
}
