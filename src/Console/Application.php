<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console;

use Jean85\PrettyVersions;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Console\Command\FindCommand;
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

        if ($this->isVersionPrintedElsewhere($input) === false) {
            // always print name version to more debug info
            $output->writeln($this->getLongVersion());
        }

        $configPath = $this->getConfigPath($input);
        if ($configPath !== null && file_exists($configPath)) {
            $output->writeln('Config file: ' . realpath($configPath));
        }

        return parent::doRun($input, $output);
    }

    protected function getDefaultInputDefinition(): InputDefinition
    {
        $inputDefinition = parent::getDefaultInputDefinition();
        $this->addExtraOptions($inputDefinition);

        return $inputDefinition;
    }

    private function getPrettyVersion(): string
    {
        $version = PrettyVersions::getVersion('symplify/easy-coding-standard');
        return $version->getPrettyVersion();
    }

    private function isVersionPrintedElsewhere(InputInterface $input): bool
    {
        return $input->hasParameterOption('--version') !== false || $input->getFirstArgument() === null;
    }

    private function getConfigPath(InputInterface $input): ?string
    {
        if ($input->getParameterOption('--config')) {
            return $input->getParameterOption('--config');
        }

        return ConfigFileFinder::provide('ecs');
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
