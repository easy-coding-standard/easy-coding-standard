<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Style;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @api
 */
final class SymfonyStyleFactory
{
    public static function create(): SymfonyStyle
    {
        // to prevent missing argv indexes
        if (! isset($_SERVER['argv'])) {
            $_SERVER['argv'] = [];
        }

        $argvInput = new ArgvInput();
        $consoleOutput = new ConsoleOutput();

        self::applySymfonyConsoleArgs($argvInput, $consoleOutput);

        // --debug is called
        if ($argvInput->hasParameterOption('--debug')) {
            $consoleOutput->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
        }

        // disable output for tests
        if (self::isPHPUnitRun()) {
            $consoleOutput->setVerbosity(OutputInterface::VERBOSITY_QUIET);
        }

        return new SymfonyStyle($argvInput, $consoleOutput);
    }

    /**
     * Never ever used static methods if not neccesary, this is just handy for tests + src to prevent duplication.
     */
    private static function isPHPUnitRun(): bool
    {
        return defined('PHPUNIT_COMPOSER_INSTALL') || defined('__PHPUNIT_PHAR__');
    }

    /**
     * This method was derived from the `Application::configureIO` method of the Symfony Console
     * component, under the MIT license. See NOTICE for the full license.
     *
     * @see https://github.com/symfony/console/blob/0aa29ca177f432ab68533432db0de059f39c92ae/Application.php#L905
     */
    private static function applySymfonyConsoleArgs(InputInterface $input, OutputInterface $output): void
    {
        $enableAnsi = $input->hasParameterOption(['--ansi'], true);
        $disableAnsi = $input->hasParameterOption(['--no-ansi'], true);

        match (true) {
            $enableAnsi => $output->setDecorated(true),
            $disableAnsi => $output->setDecorated(false),
            default => null,
        };

        $enableQuiet = $input->hasParameterOption(['--quiet', '-q'], true);

        $isVVV = $input->hasParameterOption('-vvv', true);
        $isVV = $input->hasParameterOption('-vv', true);
        $isV = $input->hasParameterOption('-v', true);

        match (true) {
            $enableQuiet => $output->setVerbosity(OutputInterface::VERBOSITY_QUIET),
            $isVVV => $output->setVerbosity(OutputInterface::VERBOSITY_DEBUG),
            $isVV => $output->setVerbosity(OutputInterface::VERBOSITY_VERY_VERBOSE),
            $isV => $output->setVerbosity(OutputInterface::VERBOSITY_VERBOSE),
            default => null,
        };
    }
}
