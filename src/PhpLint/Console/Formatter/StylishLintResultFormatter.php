<?php
declare(strict_types=1);

namespace PhpLint\Console\Formatter;

use PhpLint\Linter\LintResult;
use PhpLint\Rules\RuleSeverity;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\OutputInterface;

class StylishLintResultFormatter implements LintResultFormatter
{
    const NAME = 'stylish';

    /**
     * @inheritdoc
     */
    public function formatResult(LintResult $lintResult, OutputInterface $output)
    {
        if (count($lintResult) === 0) {
            return;
        }

        $formatter = $output->getFormatter();
        $formatter->setStyle(
            'lint_result-stylish-default',
            new OutputFormatterStyle('default', 'default')
        );
        $formatter->setStyle(
            'lint_result-stylish-filename',
            new OutputFormatterStyle('default', 'default', ['underscore'])
        );
        $formatter->setStyle(
            'lint_result-stylish-warning',
            new OutputFormatterStyle('yellow', 'default')
        );
        $formatter->setStyle(
            'lint_result-stylish-error',
            new OutputFormatterStyle('red', 'default')
        );

        // Prepend some empty lines
        $output->writeln('');
        $output->writeln('');

        $errorCount = 0;
        $warningCount = 0;
        $summaryColor = 'yellow';
        foreach ($lintResult->getFilenames() as $filename) {
            // Collect the table rows
            $tableRows = [];
            foreach ($lintResult->getViolations($filename) as $violation) {
                $tableRows[] = [
                    $violation->getLocation()->__toString(),
                    $violation->getSeverity(),
                    $violation->getMessage(),
                    $violation->getRuleId(),
                ];
            }
            if (count($tableRows) === 0) {
                continue;
            }

            // Print the formatted row values
            $output->writeln('<lint_result-stylish-filename>' . $filename . '</lint_result-stylish-filename>');
            foreach (self::formatTableData($tableRows) as $row) {
                $output->write('  <lint_result-stylish-default>' . $row[0] . '</lint_result-stylish-default>');
                if (trim($row[1]) === RuleSeverity::SEVERITY_ERROR) {
                    $errorCount += 1;
                    $summaryColor = 'red';
                    $output->write('  <lint_result-stylish-error>' . $row[1] . '</lint_result-stylish-error>');
                } else {
                    $warningCount += 1;
                    $output->write('  <lint_result-stylish-warning>' . $row[1] . '</lint_result-stylish-warning>');
                }
                $output->write('  <lint_result-stylish-default>' . $row[2] . '</lint_result-stylish-default>');
                $output->writeln('  <lint_result-stylish-default>' . $row[3] . '</lint_result-stylish-default>');
            }
            $output->writeln('');
        }

        // Print a summary
        $formatter->setStyle(
            'lint_result-stylish-summary',
            new OutputFormatterStyle($summaryColor, 'default', ['bold'])
        );
        $output->writeln(sprintf(
            "<lint_result-stylish-summary>\xE2\x9C\x96 %d %s (%d %s, %d %s)</lint_result-stylish-summary>",
            count($lintResult),
            self::pluralize('problem', count($lintResult)),
            $errorCount,
            self::pluralize('error', $errorCount),
            $warningCount,
            self::pluralize('warning', $warningCount)
        ));

        // Append an empty lines
        $output->writeln('');
    }

    /**
     * @param array $tableRows
     * @return array
     */
    protected static function formatTableData(array $tableRows): array
    {
        if (count($tableRows) === 0) {
            return $tableRows;
        }

        for ($colIndex = 0; $colIndex < count($tableRows[0]); $colIndex += 1) {
            // Calculate the column width
            $colWidth = 0;
            foreach ($tableRows as &$row) {
                $colWidth = max($colWidth, mb_strlen($row[$colIndex]));
            }

            // Pad all values of the column to the calculated width
            foreach ($tableRows as &$row) {
                $row[$colIndex] = str_pad(trim($row[$colIndex]), $colWidth);
            }
        }

        return $tableRows;
    }

    /**
     * @param string $word
     * @param int $count
     * @return string
     */
    protected static function pluralize(string $word, int $count): string
    {
        return ($count === 1) ? $word : ($word . 's');
    }
}
