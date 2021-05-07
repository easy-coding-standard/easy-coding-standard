<?php

namespace Symplify\EasyCodingStandard\Console\Style;

use ECSPrefix20210507\Symfony\Component\Console\Application;
use ECSPrefix20210507\Symfony\Component\Console\Input\ArgvInput;
use ECSPrefix20210507\Symfony\Component\Console\Output\ConsoleOutput;
use ECSPrefix20210507\Symfony\Component\Console\Output\OutputInterface;
use ECSPrefix20210507\Symfony\Component\Console\Terminal;
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
    /**
     * @param \ECSPrefix20210507\Symfony\Component\Console\Terminal $terminal
     */
    public function __construct($terminal)
    {
        $this->privatesCaller = new PrivatesCaller();
        $this->terminal = $terminal;
    }
    /**
     * @return \Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle
     */
    public function create()
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
        return new \Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle($argvInput, $consoleOutput, $this->terminal);
    }
}
