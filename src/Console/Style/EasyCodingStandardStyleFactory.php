<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Style;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Terminal;
use Symplify\EasyTesting\PHPUnit\StaticPHPUnitEnvironment;
use Symplify\PackageBuilder\Reflection\PrivatesCaller;

final class EasyCodingStandardStyleFactory
{
    /**
     * @var PrivatesCaller
     */
    private $privatesCaller;

    /**
     * @var Terminal
     */
    private $terminal;

    public function __construct(Terminal $terminal)
    {
        $this->privatesCaller = new PrivatesCaller();
        $this->terminal = $terminal;
    }

    public function create(): EasyCodingStandardStyle
    {
        $argvInput = new ArgvInput();
        $consoleOutput = new ConsoleOutput();

        // to configure all -v, -vv, -vvv options without memory-lock to Application run() arguments
        $this->privatesCaller->callPrivateMethod(new Application(), 'configureIO', [$argvInput, $consoleOutput]);

        // --debug is called
        if ($argvInput->hasParameterOption('--debug')) {
            $consoleOutput->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
        }

        // disable output for tests
        if (StaticPHPUnitEnvironment::isPHPUnitRun()) {
            $consoleOutput->setVerbosity(OutputInterface::VERBOSITY_QUIET);
        }

        return new EasyCodingStandardStyle($argvInput, $consoleOutput, $this->terminal);
    }
}
