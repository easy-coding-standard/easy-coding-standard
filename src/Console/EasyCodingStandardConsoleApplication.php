<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console;

use Composer\XdebugHandler\XdebugHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Bootstrap\NoCheckersLoaderReporter;
use Symplify\EasyCodingStandard\Configuration\Exception\NoCheckersLoadedException;
use Symplify\EasyCodingStandard\Console\Command\CheckCommand;
use Symplify\EasyCodingStandard\Console\Output\ConsoleOutputFormatter;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\PackageBuilder\Composer\PackageVersionProvider;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\SymplifyKernel\Console\AbstractSymplifyConsoleApplication;
use Throwable;

final class EasyCodingStandardConsoleApplication extends AbstractSymplifyConsoleApplication
{
    /**
     * @var NoCheckersLoaderReporter
     */
    private $noCheckersLoaderReporter;

    /**
     * @param Command[] $commands
     */
    public function __construct(NoCheckersLoaderReporter $noCheckersLoaderReporter, array $commands)
    {
        $packageVersionProvider = new PackageVersionProvider();
        $version = $packageVersionProvider->provide('symplify/easy-coding-standard');

        parent::__construct($commands, 'EasyCodingStandard', $version);
        $this->noCheckersLoaderReporter = $noCheckersLoaderReporter;
        $this->setDefaultCommand(CommandNaming::classToName(CheckCommand::class));
    }

    public function doRun(InputInterface $input, OutputInterface $output): int
    {
        // @fixes https://github.com/rectorphp/rector/issues/2205
        $isXdebugAllowed = $input->hasParameterOption('--xdebug');
        if (! $isXdebugAllowed && ! defined('PHPUNIT_COMPOSER_INSTALL')) {
            $xdebugHandler = new XdebugHandler('ecs');
            $xdebugHandler->check();
            unset($xdebugHandler);
        }

        // skip in this case, since generate content must be clear from meta-info
        if ($this->shouldPrintMetaInformation($input)) {
            $output->writeln($this->getLongVersion());
        }

        return parent::doRun($input, $output);
    }

    public function renderThrowable(Throwable $throwable, OutputInterface $output): void
    {
        if (is_a($throwable, NoCheckersLoadedException::class)) {
            $this->noCheckersLoaderReporter->report();
            return;
        }

        parent::renderThrowable($throwable, $output);
    }

    protected function getDefaultInputDefinition(): InputDefinition
    {
        $inputDefinition = parent::getDefaultInputDefinition();
        $this->addExtraOptions($inputDefinition);

        return $inputDefinition;
    }

    private function shouldPrintMetaInformation(InputInterface $input): bool
    {
        $hasNoArguments = $input->getFirstArgument() === null;
        $hasVersionOption = $input->hasParameterOption('--version');

        if ($hasVersionOption) {
            return false;
        }

        if ($hasNoArguments) {
            return false;
        }
        $outputFormat = $input->getParameterOption('--' . Option::OUTPUT_FORMAT);

        return $outputFormat === ConsoleOutputFormatter::NAME;
    }

    private function addExtraOptions(InputDefinition $inputDefinition): void
    {
        $inputDefinition->addOption(new InputOption(
            Option::XDEBUG,
            null,
            InputOption::VALUE_NONE,
            'Allow running xdebug'
        ));

        $inputDefinition->addOption(new InputOption(
            Option::DEBUG,
            null,
            InputOption::VALUE_NONE,
            'Run in debug mode (alias for "-vvv")'
        ));
    }
}
