<?php
declare(strict_types=1);

namespace PhpLint\Console\Formatter;

use PhpLint\Linter\LintResult;
use Symfony\Component\Console\Output\OutputInterface;

interface LintResultFormatter
{
    /**
     * @param LintResult $lintResult
     * @param OutputInterface $output
     */
    public function formatResult(LintResult $lintResult, OutputInterface $output);
}
