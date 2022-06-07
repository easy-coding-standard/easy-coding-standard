<?php

declare (strict_types=1);
namespace ECSPrefix20220607\Symplify\EasyCodingStandard\Console\Style;

use ECSPrefix20220607\Symfony\Component\Console\Application;
use ECSPrefix20220607\Symfony\Component\Console\Input\ArgvInput;
use ECSPrefix20220607\Symfony\Component\Console\Output\ConsoleOutput;
use ECSPrefix20220607\Symfony\Component\Console\Output\OutputInterface;
use ECSPrefix20220607\Symfony\Component\Console\Terminal;
use ECSPrefix20220607\Symplify\EasyTesting\PHPUnit\StaticPHPUnitEnvironment;
use ECSPrefix20220607\Symplify\PackageBuilder\Reflection\PrivatesCaller;
final class EasyCodingStandardStyleFactory
{
    /**
     * @var \Symplify\PackageBuilder\Reflection\PrivatesCaller
     */
    private $privatesCaller;
    /**
     * @var \Symfony\Component\Console\Terminal
     */
    private $terminal;
    public function __construct(Terminal $terminal)
    {
        $this->terminal = $terminal;
        $this->privatesCaller = new PrivatesCaller();
    }
    public function create() : EasyCodingStandardStyle
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
