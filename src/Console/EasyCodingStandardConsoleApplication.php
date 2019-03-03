<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Console\Command\FindCommand;
use Symplify\EasyCodingStandard\Console\Output\JsonOutputFormatter;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\HelpfulApplicationTrait;

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
        $this->configuration->setConfigFilePathFromInput($input);

        // skip in this case, since generate content must be clear from meta-info
        if ($input->getFirstArgument() === CommandNaming::classToName(FindCommand::class)) {
            return parent::doRun($input, $output);
        }

        if ($this->shouldPrintMetaInformation($input)) {
            $output->writeln($this->getLongVersion());
        }

        $configPath = $this->configuration->getConfigFilePath();
        if ($this->configExists($configPath) && $this->shouldPrintMetaInformation($input)) {
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

    private function shouldPrintMetaInformation(InputInterface $input): bool
    {
        $hasNoArguments = $input->getFirstArgument() === null;
        $hasVersionOption = $input->hasParameterOption('--version');
        $hasJsonOutput = $input->getParameterOption('--output-format') === JsonOutputFormatter::NAME;

        return ! ($hasVersionOption || $hasNoArguments || $hasJsonOutput);
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
