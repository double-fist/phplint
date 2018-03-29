<?php
declare(strict_types=1);

namespace PhpLint\Linter;

use PhpLint\Ast\NodeTraverser;
use PhpLint\Configuration\Configuration;
use PhpLint\Configuration\ConfigurationLoader;
use PhpLint\Linter\Directive\DirectiveParser;
use PhpLint\PhpParser\PhpParser;
use PhpLint\Plugin\PluginLoader;

class Linter
{
    /**
     * @param PhpParser $parser
     */
    private $parser;

    /**
     * @param PluginLoader $pluginLoader
     */
    private $pluginLoader;

    /**
     * @param ConfigurationLoader $configLoader
     */
    private $configLoader;

    public function __construct()
    {
        $this->parser = new PhpParser();
        $this->pluginLoader = new PluginLoader();
        $this->configLoader = new ConfigurationLoader($this->pluginLoader);
    }

    /**
     * Lints all files at the given $filePaths and returns the result.
     *
     * @param string[] $filePaths
     * @param Configuration $extraConfig
     * @param LintResult|null $lintResult,
     * @return LintResult
     */
    public function lintFilesAtPaths(
        array $filePaths,
        Configuration $extraConfig,
        LintResult $lintResult = null
    ): LintResult {
        $fileTraverser = new FileTraverser($filePaths, $this->configLoader, $extraConfig);
        foreach ($fileTraverser as $filePath) {
            $this->lintFileAtPath($filePath, $fileTraverser->getCurrentFileConfig(), $lintResult);
        }

        return $lintResult;
    }

    /**
     * Lints the file at the given $filePath and returns the result. If a $lintResult is passed, that instance is used
     * to collect the found violations.
     *
     * @param string $filePath
     * @param Configuration $config
     * @param LintResult|null $lintResult
     * @return LintResult
     * @throws LintException if the given $filePath does not point to a readable PHP file.
     */
    public function lintFileAtPath(
        string $filePath,
        Configuration $config,
        LintResult $lintResult = null
    ): LintResult {
        // Check that path points to a PHP file
        if (!is_file($filePath) || pathinfo($filePath)['extension'] !== 'php') {
            throw LintException::invalidPath($filePath);
        }

        $sourceCode = file_get_contents($filePath);
        if ($sourceCode === false) {
            throw LintException::failedToReadFile($filePath);
        }

        return $this->lintCode($sourceCode, $config, $lintResult, $filePath);
    }

    /**
     * Lints the given $sourceCode and returns the result. If a $lintResult is passed, that instance is used to collect
     * the found violations.
     *
     * @param string $sourceCode
     * @param Configuration $config
     * @param LintResult|null $lintResult
     * @param string|null $filePath
     * @return LintResult
     */
    public function lintCode(
        string $sourceCode,
        Configuration $config,
        LintResult $lintResult = null,
        string $filePath = null
    ): LintResult {
        if (!$lintResult) {
            $lintResult = new LintResult();
        }

        // Parse source code
        $sourceContext = $this->parser->parse($sourceCode, $filePath);

        // TODO: Apply any inline configuration found in the source code
        $fileConfig = $config;

        // Collect all inline directives (e.g. 'phplint-disable')
        $directiveParser = new DirectiveParser($sourceContext);
        $directives = $directiveParser->getDirectives();
        $disableDirectives = $directiveParser->getDisableDirectives();

        // Run rules on all nodes of the source context
        $ruleProcessor = new RuleProcessor($fileConfig);
        $nodeTraverser = new NodeTraverser($sourceContext->getAst());
        foreach ($nodeTraverser as $node) {
            $ruleProcessor->runRules($node, $sourceContext, $lintResult);
        }

        // Apply the disable directives to the result
        $lintResult->applyDisableDirectives($sourceContext->getPath(), $disableDirectives);

        return $lintResult;
    }
}
