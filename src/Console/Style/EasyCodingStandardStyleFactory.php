<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Console\Style;

use ECSPrefix202408\Symfony\Component\Console\Input\ArgvInput;
use ECSPrefix202408\Symfony\Component\Console\Output\ConsoleOutput;
use ECSPrefix202408\Symfony\Component\Console\Output\OutputInterface;
use ECSPrefix202408\Symfony\Component\Console\Terminal;
/**
 * @api
 */
final class EasyCodingStandardStyleFactory
{
    /**
     * @readonly
     * @var \Symfony\Component\Console\Terminal
     */
    private $terminal;
    public function __construct(Terminal $terminal)
    {
        $this->terminal = $terminal;
    }
    /**
     * @api
     */
    public function create() : \Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle
    {
        $argvInput = new ArgvInput();
        $consoleOutput = new ConsoleOutput();
        // --debug is called
        if ($argvInput->hasParameterOption('--debug')) {
            $consoleOutput->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
        }
        // disable output for tests
        if (\defined('PHPUNIT_COMPOSER_INSTALL')) {
            $consoleOutput->setVerbosity(OutputInterface::VERBOSITY_QUIET);
        }
        return new \Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle($argvInput, $consoleOutput, $this->terminal);
    }
}
