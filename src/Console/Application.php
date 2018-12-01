<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console;

use Jean85\PrettyVersions;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Console\Command\FindCommand;
use Symplify\EasyCodingStandard\Console\Output\JsonOutputFormatter;
use Symplify\PackageBuilder\Configuration\ConfigFileFinder;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use function Safe\realpath;

final class Application extends SymfonyApplication
{
    public function __construct()
    {
        parent::__construct('EasyCodingStandard', $this->getPrettyVersion());
    }

    public function doRun(InputInterface $input, OutputInterface $output): int
    {
        // skip in this case, since generate content must be clear from meta-info
        if ($input->getFirstArgument() === CommandNaming::classToName(FindCommand::class)) {
            return parent::doRun($input, $output);
        }

        if ($this->shouldPrintMetaInformation($input)) {
            $output->writeln($this->getLongVersion());
        }

        $configPath = $this->getConfigPath($input);
        if ($this->configExists($configPath) && $this->shouldPrintMetaInformation($input)) {
            $output->writeln('Config file: ' . realpath($configPath));
        }

        return parent::doRun($input, $output);
    }

    public function getConfigPath(InputInterface $input): ?string
    {
        if ($input->getParameterOption('--config')) {
            return $input->getParameterOption('--config');
        }

        return ConfigFileFinder::provide('ecs');
    }

    public function getPrettyVersion(): string
    {
        $version = PrettyVersions::getVersion('symplify/easy-coding-standard');
        return $version->getPrettyVersion();
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
        $hasJsonOutput = $input->getParameterOption('--output-format') === JsonOutputFormatter::NAME;

        return ($hasVersionOption || $hasNoArguments || $hasJsonOutput) === false;
    }

    private function configExists(?string $configPath): bool
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
            'level',
            'l',
            InputOption::VALUE_REQUIRED,
            'Finds config by shortcut name.'
        ));

        $inputDefinition->addOption(new InputOption(
            'debug',
            null,
            InputOption::VALUE_NONE,
            'Run in debug mode (alias for "-vvv")'
        ));
    }
}
