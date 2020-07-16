<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console;

use Composer\XdebugHandler\XdebugHandler;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Console\Command\FindCommand;
use Symplify\EasyCodingStandard\Console\Output\ConsoleOutputFormatter;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\HelpfulApplicationTrait;
use Symplify\SmartFileSystem\SmartFileInfo;

final class EasyCodingStandardConsoleApplication extends Application
{
    use HelpfulApplicationTrait;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @param Command[] $commands
     */
    public function __construct(Configuration $configuration, array $commands)
    {
        parent::__construct('EasyCodingStandard', $configuration->getPrettyVersion());

        $this->configuration = $configuration;
        $this->addCommands($commands);
    }

    public function doRun(InputInterface $input, OutputInterface $output): int
    {
        // @fixes https://github.com/rectorphp/rector/issues/2205
        $isXdebugAllowed = $input->hasParameterOption('--xdebug');
        if (! $isXdebugAllowed && ! defined('PHPUNIT_COMPOSER_INSTALL')) {
            $xdebug = new XdebugHandler('ecs', '--ansi');
            $xdebug->check();
            unset($xdebug);
        }

        // skip in this case, since generate content must be clear from meta-info
        if ($input->getFirstArgument() === CommandNaming::classToName(FindCommand::class)) {
            return parent::doRun($input, $output);
        }

        if ($this->shouldPrintMetaInformation($input)) {
            $output->writeln($this->getLongVersion());
        }

        $configPath = $this->configuration->getFirstResolverConfig();
        if ($this->doesConfigExist($configPath) && $this->shouldPrintMetaInformation($input)) {
            $configFileInfo = new SmartFileInfo($configPath);
            $output->writeln('Config file: ' . $configFileInfo->getRelativeFilePathFromCwd());
        }

        return parent::doRun($input, $output);
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
        $isConsoleOutput = $input->getParameterOption('--output-format') === ConsoleOutputFormatter::NAME;

        return ! $hasVersionOption && ! $hasNoArguments && $isConsoleOutput;
    }

    private function doesConfigExist(?string $configPath): bool
    {
        return $configPath !== null && file_exists($configPath);
    }

    private function addExtraOptions(InputDefinition $inputDefinition): void
    {
        $inputDefinition->addOption(new InputOption(
            'config',
            'c',
            InputOption::VALUE_REQUIRED,
            'Path to config file.',
            '(ecs|easy-coding-standard).(yml|yaml)'
        ));

        $inputDefinition->addOption(new InputOption(
            'xdebug',
            null,
            InputOption::VALUE_NONE,
            'Allow running xdebug'
        ));

        $inputDefinition->addOption(new InputOption('set', 's', InputOption::VALUE_REQUIRED, 'Load provided set'));

        $inputDefinition->addOption(new InputOption(
            'debug',
            null,
            InputOption::VALUE_NONE,
            'Run in debug mode (alias for "-vvv")'
        ));
    }
}
