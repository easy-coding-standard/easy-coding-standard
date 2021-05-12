<?php

namespace Symplify\EasyCodingStandard\Console\Style;

use ECSPrefix20210512\Symfony\Component\Console\Application;
use ECSPrefix20210512\Symfony\Component\Console\Input\ArgvInput;
use ECSPrefix20210512\Symfony\Component\Console\Output\ConsoleOutput;
use ECSPrefix20210512\Symfony\Component\Console\Output\OutputInterface;
use ECSPrefix20210512\Symfony\Component\Console\Terminal;
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
    public function __construct(\ECSPrefix20210512\Symfony\Component\Console\Terminal $terminal)
    {
        $this->privatesCaller = new \Symplify\PackageBuilder\Reflection\PrivatesCaller();
        $this->terminal = $terminal;
    }
    /**
     * @return \Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle
     */
    public function create()
    {
        $argvInput = new \ECSPrefix20210512\Symfony\Component\Console\Input\ArgvInput();
        $consoleOutput = new \ECSPrefix20210512\Symfony\Component\Console\Output\ConsoleOutput();
        // to configure all -v, -vv, -vvv options without memory-lock to Application run() arguments
        $this->privatesCaller->callPrivateMethod(new \ECSPrefix20210512\Symfony\Component\Console\Application(), 'configureIO', [$argvInput, $consoleOutput]);
        // --debug is called
        if ($argvInput->hasParameterOption('--debug')) {
            $consoleOutput->setVerbosity(\ECSPrefix20210512\Symfony\Component\Console\Output\OutputInterface::VERBOSITY_DEBUG);
        }
        // disable output for tests
        if (\Symplify\EasyTesting\PHPUnit\StaticPHPUnitEnvironment::isPHPUnitRun()) {
            $consoleOutput->setVerbosity(\ECSPrefix20210512\Symfony\Component\Console\Output\OutputInterface::VERBOSITY_QUIET);
        }
        return new \Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle($argvInput, $consoleOutput, $this->terminal);
    }
}
