<?php
declare(strict_types=1);

namespace PhpLint\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

use PhpLint\Configuration\Configuration;
use PhpLint\Console\Formatter\FormatterFactory;
use PhpLint\Console\Formatter\StylishLintResultFormatter;
use PhpLint\Linter\Linter;
use PhpLint\Linter\LintResult;
use PhpLint\Util\IgnoreFileParser;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Webmozart\Glob\Glob;

class PhpLintCommand extends Command
{
    const ARG_NAME_LINT_PATH = 'lint-path';
    const OPTION_NAME_ERRORS_ONLY = 'errors-only';
    const OPTION_NAME_FORMAT = 'format';
    const OPTION_NAME_IGNORE_PATTERN = 'ignore-pattern';

    const DEFAULT_IGNORE_PATTERNS = [
        '/vendor/*',
    ];

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('phplint');
        $this->setDefinition([
            new InputArgument(
                self::ARG_NAME_LINT_PATH,
                InputArgument::OPTIONAL,
                'The path whose files shall be linted',
                '.'
            ),
            new InputOption(
                self::OPTION_NAME_ERRORS_ONLY,
                null,
                InputOption::VALUE_NONE,
                'Report errors only'
            ),
            new InputOption(
                self::OPTION_NAME_FORMAT,
                'f',
                InputOption::VALUE_OPTIONAL,
                'Use a specific output format - default: stylish',
                StylishLintResultFormatter::NAME
            ),
            new InputOption(
                self::OPTION_NAME_IGNORE_PATTERN,
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Pattern of files to ignore (in addition to those in .phplintignore)',
                []
            ),
        ]);
        $this->setDescription('Lints the file(s) at the specified path');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Find all files at the given path that are not ignores
        $lintPath = $input->getArgument(self::ARG_NAME_LINT_PATH);
        $lintPath = $this->normalizeLintPath($lintPath);
        $ignorePatterns = $this->getIgnorePatterns($input->getOption(self::OPTION_NAME_IGNORE_PATTERN));
        $phpFilePaths = $this->findPhpFiles($lintPath, $ignorePatterns);
        if (count($phpFilePaths) === 0) {
            $output->writeln('Nothing to lint at path' . $lintPath);

            return;
        }

        // TODO: Create config from the CLI options
        $config = new Configuration([]);

        // Prepare lint result, respecting the passed options
        $errorsOnly = $input->getOption(self::OPTION_NAME_ERRORS_ONLY);
        $lintResult = new LintResult($errorsOnly);

        // Prepare the result formatter
        $formatterFactory = new FormatterFactory();
        $outputFormat = $input->getOption(self::OPTION_NAME_FORMAT);
        $resultFormatter = $formatterFactory->createLintResultFormatter($outputFormat);

        // Lint all PHP files found in the path
        $linter = new Linter();
        $lintResult = $linter->lintFilesAtPaths($phpFilePaths, $config, $lintResult);

        // Format the result
        $resultFormatter->formatResult($lintResult, $output);

        // Return a non-zero status code if any violations with 'error' severity were reported
        return ($lintResult->containsErrors()) ? 1 : 0;
    }

    /**
     * @param string $lintPath
     * @return string
     * @throws IOException
     */
    private function normalizeLintPath(string $lintPath): string
    {
        $filesystem = new Filesystem();
        if (!$filesystem->isAbsolutePath($lintPath)) {
            if ($lintPath === '.' || $lintPath === './') {
                $lintPath = getcwd();
            } else {
                $lintPath = getcwd() . '/' . $lintPath;
            }
        }
        if (!$filesystem->exists($lintPath)) {
            throw new IOException('File file or directory does not exist.', 0, null, $lintPath);
        }
        if (!is_readable($lintPath)) {
            throw new IOException('File file or directory is not readable.', 0, null, $lintPath);
        }

        return $lintPath;
    }

    /**
     * @param string $path
     * @param string[] $ignorePatterns
     * @param string[]
     */
    private function findPhpFiles(string $path, array $ignorePatterns): array
    {
        if (is_file($path)) {
            return [$path];
        }

        // Revert the order of the ignore patterns. That way we can just use the first matching pattern to decide
        // whether to ignore or keep a file.
        $reversedIgnorePatterns = array_reverse($ignorePatterns);

        // Create a directory iterator for finding all PHP files that are not ignored by any pattern
        $directoryIterator = new RecursiveDirectoryIterator($path);
        $filterIterator = new RecursiveCallbackFilterIterator(
            $directoryIterator,
            function ($current, $key, $iterator) use ($reversedIgnorePatterns) {
                // Test all ignore patterns on the path to support including rules defined after a matching
                // excluding rule
                $ignoreFile = false;
                foreach ($reversedIgnorePatterns as $absolutePattern => $isIncluding) {
                    if (Glob::match($current->getPathname(), $absolutePattern)) {
                        $ignoreFile = !$isIncluding;
                        break;
                    }
                }
                if ($ignoreFile) {
                    return false;
                }

                // Allow recursion
                if ($iterator->hasChildren()) {
                    return true;
                }

                return $current->isFile() && $current->getExtension() === 'php';
            }
        );

        // List and sort the file paths
        $phpFilePaths = [];
        foreach (new RecursiveIteratorIterator($filterIterator) as $fileInfo) {
            $phpFilePaths[] = $fileInfo->getPathname();
        }
        sort($phpFilePaths);

        return $phpFilePaths;
    }

    /**
     * @param string[] $cliIgnorePatterns
     * @return string[]
     */
    private function getIgnorePatterns(array $cliIgnorePatterns): array
    {
        // Check the current working directory for a .phplintignore file
        $workingDir = getcwd();
        $ignoreFileParser = new IgnoreFileParser();
        $fileIgnorePatterns = $ignoreFileParser->readIgnorePatterns($workingDir);

        // Combine the ignore patterns in the order 'default', 'file', 'cli'
        $ignorePatterns = array_merge(
            self::DEFAULT_IGNORE_PATTERNS,
            $fileIgnorePatterns,
            $cliIgnorePatterns
        );

        // Determine whether the patterns exclude or include ('!') the matching files and prefix them with the current
        // working directory, because the used glob lib only supports absolute paths. Using the glob as key already
        // eliminates duplicates of exact same patterns.
        $absoluteIgnorePatterns = [];
        foreach ($ignorePatterns as $pattern) {
            $isIncluding = mb_substr($pattern, 0, 1) === '!';
            $absolutePattern = $workingDir . '/' . ltrim($pattern, '/!');
            $absoluteIgnorePatterns[$absolutePattern] = $isIncluding;
        }

        return $absoluteIgnorePatterns;
    }
}
