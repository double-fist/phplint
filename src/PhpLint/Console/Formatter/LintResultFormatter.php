<?php
declare(strict_types=1);

namespace PhpLint\Console\Formatter;

use PhpLint\Linter\LintResultCollection;
use Symfony\Component\Console\Output\OutputInterface;

interface LintResultFormatter
{
    /**
     * @param LintResultCollection $lintResult
     * @param OutputInterface $output
     */
    public function formatResult(LintResultCollection $lintResult, OutputInterface $output);
}
