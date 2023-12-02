<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Console\Style;

use ECSPrefix202312\Symfony\Component\Console\Input\ArgvInput;
use ECSPrefix202312\Symfony\Component\Console\Output\ConsoleOutput;
use ECSPrefix202312\Symfony\Component\Console\Output\OutputInterface;
use ECSPrefix202312\Symfony\Component\Console\Style\SymfonyStyle;
/**
 * @api
 */
final class SymfonyStyleFactory
{
    public function create() : SymfonyStyle
    {
        // to prevent missing argv indexes
        if (!isset($_SERVER['argv'])) {
            $_SERVER['argv'] = [];
        }
        $argvInput = new ArgvInput();
        $consoleOutput = new ConsoleOutput();
        // --debug is called
        if ($argvInput->hasParameterOption('--debug')) {
            $consoleOutput->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
        }
        // disable output for tests
        if ($this->isPHPUnitRun()) {
            $consoleOutput->setVerbosity(OutputInterface::VERBOSITY_QUIET);
        }
        return new SymfonyStyle($argvInput, $consoleOutput);
    }
    /**
     * Never ever used static methods if not neccesary, this is just handy for tests + src to prevent duplication.
     */
    private function isPHPUnitRun() : bool
    {
        return \defined('ECSPrefix202312\\PHPUNIT_COMPOSER_INSTALL') || \defined('ECSPrefix202312\\__PHPUNIT_PHAR__');
    }
}
