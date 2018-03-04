<?php
declare(strict_types=1);

namespace PhpLint\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

class PhpLintCommand extends Command
{
    const ARG_NAME_LINT_PATH = 'lint-path';

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
        ]);
        $this->setDescription('Lints the file(s) at the specified path');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Normalize and check lint path
        $filesystem = new Filesystem();
        $lintPath = $input->getArgument(self::ARG_NAME_LINT_PATH);
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

        $output->writeln('Linting all files at path ' . $lintPath);

        // TODO: Lint all files at specified path

        $output->writeln('Done!');
    }
}
