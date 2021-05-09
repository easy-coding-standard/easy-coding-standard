<?php

namespace Symplify\PackageBuilder\Console\Style;

use ECSPrefix20210509\Symfony\Component\Console\Application;
use ECSPrefix20210509\Symfony\Component\Console\Input\ArgvInput;
use ECSPrefix20210509\Symfony\Component\Console\Output\ConsoleOutput;
use ECSPrefix20210509\Symfony\Component\Console\Output\OutputInterface;
use ECSPrefix20210509\Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyTesting\PHPUnit\StaticPHPUnitEnvironment;
use Symplify\PackageBuilder\Reflection\PrivatesCaller;
final class SymfonyStyleFactory
{
    /**
     * @var PrivatesCaller
     */
    private $privatesCaller;
    public function __construct()
    {
        $this->privatesCaller = new \Symplify\PackageBuilder\Reflection\PrivatesCaller();
    }
    /**
     * @return \Symfony\Component\Console\Style\SymfonyStyle
     */
    public function create()
    {
        // to prevent missing argv indexes
        if (!isset($_SERVER['argv'])) {
            $_SERVER['argv'] = [];
        }
        $argvInput = new \ECSPrefix20210509\Symfony\Component\Console\Input\ArgvInput();
        $consoleOutput = new \ECSPrefix20210509\Symfony\Component\Console\Output\ConsoleOutput();
        // to configure all -v, -vv, -vvv options without memory-lock to Application run() arguments
        $this->privatesCaller->callPrivateMethod(new \ECSPrefix20210509\Symfony\Component\Console\Application(), 'configureIO', [$argvInput, $consoleOutput]);
        // --debug is called
        if ($argvInput->hasParameterOption('--debug')) {
            $consoleOutput->setVerbosity(\ECSPrefix20210509\Symfony\Component\Console\Output\OutputInterface::VERBOSITY_DEBUG);
        }
        // disable output for tests
        if (\Symplify\EasyTesting\PHPUnit\StaticPHPUnitEnvironment::isPHPUnitRun()) {
            $consoleOutput->setVerbosity(\ECSPrefix20210509\Symfony\Component\Console\Output\OutputInterface::VERBOSITY_QUIET);
        }
        return new \ECSPrefix20210509\Symfony\Component\Console\Style\SymfonyStyle($argvInput, $consoleOutput);
    }
}
