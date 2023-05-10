<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Console\Style;

use ECSPrefix202305\Symfony\Component\Console\Application;
use ECSPrefix202305\Symfony\Component\Console\Input\ArgvInput;
use ECSPrefix202305\Symfony\Component\Console\Output\ConsoleOutput;
use ECSPrefix202305\Symfony\Component\Console\Output\OutputInterface;
use ECSPrefix202305\Symfony\Component\Console\Terminal;
use ECSPrefix202305\Symplify\PackageBuilder\Reflection\PrivatesCaller;
final class EasyCodingStandardStyleFactory
{
    /**
     * @readonly
     * @var \Symplify\PackageBuilder\Reflection\PrivatesCaller
     */
    private $privatesCaller;
    /**
     * @readonly
     * @var \Symfony\Component\Console\Terminal
     */
    private $terminal;
    public function __construct(Terminal $terminal)
    {
        $this->terminal = $terminal;
        $this->privatesCaller = new PrivatesCaller();
    }
    /**
     * @api
     */
    public function create() : \Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle
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
        if (\defined('ECSPrefix202305\\PHPUNIT_COMPOSER_INSTALL')) {
            $consoleOutput->setVerbosity(OutputInterface::VERBOSITY_QUIET);
        }
        return new \Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle($argvInput, $consoleOutput, $this->terminal);
    }
}
