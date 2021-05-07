<?php

namespace Symplify\EasyCodingStandard\Console;

use ECSPrefix20210507\Composer\XdebugHandler\XdebugHandler;
use ECSPrefix20210507\Symfony\Component\Console\Command\Command;
use ECSPrefix20210507\Symfony\Component\Console\Input\InputDefinition;
use ECSPrefix20210507\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20210507\Symfony\Component\Console\Input\InputOption;
use ECSPrefix20210507\Symfony\Component\Console\Output\OutputInterface;
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
     * @param \Symplify\EasyCodingStandard\Bootstrap\NoCheckersLoaderReporter $noCheckersLoaderReporter
     */
    public function __construct($noCheckersLoaderReporter, array $commands)
    {
        $packageVersionProvider = new PackageVersionProvider();
        $version = $packageVersionProvider->provide('symplify/easy-coding-standard');
        parent::__construct($commands, 'EasyCodingStandard', $version);
        $this->noCheckersLoaderReporter = $noCheckersLoaderReporter;
        $this->setDefaultCommand(CommandNaming::classToName(CheckCommand::class));
    }
    /**
     * @param \ECSPrefix20210507\Symfony\Component\Console\Input\InputInterface $input
     * @param \ECSPrefix20210507\Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    public function doRun($input, $output)
    {
        // @fixes https://github.com/rectorphp/rector/issues/2205
        $isXdebugAllowed = $input->hasParameterOption('--xdebug');
        if (!$isXdebugAllowed && !\defined('PHPUNIT_COMPOSER_INSTALL')) {
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
     * @param \ECSPrefix20210507\Symfony\Component\Console\Output\OutputInterface $output
     * @return void
     * @param \Throwable $throwable
     */
    public function renderThrowable($throwable, $output)
    {
        if (\is_a($throwable, NoCheckersLoadedException::class)) {
            $this->noCheckersLoaderReporter->report();
            return;
        }
        parent::renderThrowable($throwable, $output);
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
     * @param \ECSPrefix20210507\Symfony\Component\Console\Input\InputInterface $input
     * @return bool
     */
    private function shouldPrintMetaInformation($input)
    {
        $hasNoArguments = $input->getFirstArgument() === null;
        $hasVersionOption = $input->hasParameterOption('--version');
        if ($hasVersionOption) {
            return \false;
        }
        if ($hasNoArguments) {
            return \false;
        }
        $outputFormat = $input->getParameterOption('--' . Option::OUTPUT_FORMAT);
        return $outputFormat === ConsoleOutputFormatter::NAME;
    }
    /**
     * @return void
     * @param \ECSPrefix20210507\Symfony\Component\Console\Input\InputDefinition $inputDefinition
     */
    private function addExtraOptions($inputDefinition)
    {
        $inputDefinition->addOption(new InputOption(Option::XDEBUG, null, InputOption::VALUE_NONE, 'Allow running xdebug'));
        $inputDefinition->addOption(new InputOption(Option::DEBUG, null, InputOption::VALUE_NONE, 'Run in debug mode (alias for "-vvv")'));
    }
}
