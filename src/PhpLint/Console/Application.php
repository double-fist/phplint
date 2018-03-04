<?php
declare(strict_types=1);

namespace PhpLint\Console;

use PhpLint\PhpLint;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends ConsoleApplication
{
    public function __construct()
    {
        parent::__construct('PHP Lint', PhpLint::VERSION);

        $this->setDefaultCommand('phplint', true);
    }

    /**
     * @return ConsoleOutput
     */
    public static function createConsoleOutput(): ConsoleOutput
    {
        $styles = [
            'warning' => new OutputFormatterStyle('black', 'yellow'),
        ];
        $formatter = new OutputFormatter(false, $styles);

        return new ConsoleOutput(ConsoleOutput::VERBOSITY_NORMAL, null, $formatter);
    }

    /**
     * @inheritDoc
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        if (!$output) {
            $output = self::createConsoleOutput();
        }

        return parent::run($input, $output);
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultCommands()
    {
        $defaultCommands = parent::getDefaultCommands();
        $defaultCommands[] = new PhpLintCommand();

        return $defaultCommands;
    }
}
