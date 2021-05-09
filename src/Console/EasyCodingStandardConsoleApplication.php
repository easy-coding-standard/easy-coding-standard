<?php

namespace Symplify\EasyCodingStandard\Console;

use Composer\XdebugHandler\XdebugHandler;
use Nette\Utils\Strings;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Console\Command\CheckCommand;
use Symplify\EasyCodingStandard\Console\Output\ConsoleOutputFormatter;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\CommandNaming;

final class EasyCodingStandardConsoleApplication extends Application
{
    /**
     * @param Command[] $commands
     */
    public function __construct(array $commands)
    {
        $version = $this->resolveEasyCodingStandardVersion();

        parent::__construct('EasyCodingStandard', $version);

        // @see https://tomasvotruba.com/blog/2020/10/26/the-bullet-proof-symfony-command-naming/
        $commandNaming = new CommandNaming();
        foreach ($commands as $command) {
            $commandName = $commandNaming->resolveFromCommand($command);
            $command->setName($commandName);
            $this->add($command);
        }

        $this->setDefaultCommand(CommandNaming::classToName(CheckCommand::class));
    }

    /**
     * @return int
     */
    public function doRun(InputInterface $input, OutputInterface $output)
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

    /**
     * @return \Symfony\Component\Console\Input\InputDefinition
     */
    protected function getDefaultInputDefinition()
    {
        $inputDefinition = parent::getDefaultInputDefinition();
        $this->addExtraOptions($inputDefinition);

        return $inputDefinition;
    }

    /**
     * @return bool
     */
    private function shouldPrintMetaInformation(InputInterface $input)
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

    /**
     * @return void
     */
    private function addExtraOptions(InputDefinition $inputDefinition)
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

    /**
     * @return string
     */
    private function resolveEasyCodingStandardVersion()
    {
        // load packages' scoped installed versions class
        if (file_exists(__DIR__ . '/../../vendor/composer/InstalledVersions.php')) {
            require_once __DIR__ . '/../../vendor/composer/InstalledVersions.php';
        }

        $installedRawData = \Composer\InstalledVersions::getRawData();
        $ecsPackageData = isset($installedRawData['versions']['symplify/easy-coding-standard']) ? $installedRawData['versions']['symplify/easy-coding-standard'] : null;
        if ($ecsPackageData === null) {
            return 'Unknown';
        }
        if (isset($ecsPackageData['replaced'])) {
            return 'replaced@' . $ecsPackageData['replaced'][0];
        }

        if ($ecsPackageData['version'] === 'dev-main') {
            if ($ecsPackageData['reference'] !== null) {
                return 'dev-main@' . Strings::substring($ecsPackageData['reference'], 0, 7);
            }

            return isset($ecsPackageData['aliases'][0]) ? $ecsPackageData['aliases'][0] : 'dev-main';
        }

        return $ecsPackageData['version'];
    }
}
