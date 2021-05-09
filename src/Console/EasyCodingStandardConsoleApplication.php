<?php

namespace Symplify\EasyCodingStandard\Console;

use ECSPrefix20210509\Composer\InstalledVersions;
use ECSPrefix20210509\Composer\XdebugHandler\XdebugHandler;
use ECSPrefix20210509\Nette\Utils\Strings;
use ECSPrefix20210509\Symfony\Component\Console\Command\Command;
use ECSPrefix20210509\Symfony\Component\Console\Input\InputDefinition;
use ECSPrefix20210509\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20210509\Symfony\Component\Console\Input\InputOption;
use ECSPrefix20210509\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Bootstrap\NoCheckersLoaderReporter;
use Symplify\EasyCodingStandard\Configuration\Exception\NoCheckersLoadedException;
use Symplify\EasyCodingStandard\Console\Command\CheckCommand;
use Symplify\EasyCodingStandard\Console\Output\ConsoleOutputFormatter;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\SymplifyKernel\Console\AbstractSymplifyConsoleApplication;
use Throwable;
final class EasyCodingStandardConsoleApplication extends \Symplify\SymplifyKernel\Console\AbstractSymplifyConsoleApplication
{
    /**
     * @var NoCheckersLoaderReporter
     */
    private $noCheckersLoaderReporter;
    /**
     * @param Command[] $commands
     */
    public function __construct(\Symplify\EasyCodingStandard\Bootstrap\NoCheckersLoaderReporter $noCheckersLoaderReporter, array $commands)
    {
        $version = $this->resolveEasyCodingStandardVersion();
        parent::__construct($commands, 'EasyCodingStandard', $version);
        $this->noCheckersLoaderReporter = $noCheckersLoaderReporter;
        $this->setDefaultCommand(\Symplify\PackageBuilder\Console\Command\CommandNaming::classToName(\Symplify\EasyCodingStandard\Console\Command\CheckCommand::class));
    }
    /**
     * @return int
     */
    public function doRun(\ECSPrefix20210509\Symfony\Component\Console\Input\InputInterface $input, \ECSPrefix20210509\Symfony\Component\Console\Output\OutputInterface $output)
    {
        // @fixes https://github.com/rectorphp/rector/issues/2205
        $isXdebugAllowed = $input->hasParameterOption('--xdebug');
        if (!$isXdebugAllowed && !\defined('PHPUNIT_COMPOSER_INSTALL')) {
            $xdebugHandler = new \ECSPrefix20210509\Composer\XdebugHandler\XdebugHandler('ecs');
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
     * @return void
     */
    public function renderThrowable(\Throwable $throwable, \ECSPrefix20210509\Symfony\Component\Console\Output\OutputInterface $output)
    {
        if (\is_a($throwable, \Symplify\EasyCodingStandard\Configuration\Exception\NoCheckersLoadedException::class)) {
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
     * @return bool
     */
    private function shouldPrintMetaInformation(\ECSPrefix20210509\Symfony\Component\Console\Input\InputInterface $input)
    {
        $hasNoArguments = $input->getFirstArgument() === null;
        $hasVersionOption = $input->hasParameterOption('--version');
        if ($hasVersionOption) {
            return \false;
        }
        if ($hasNoArguments) {
            return \false;
        }
        $outputFormat = $input->getParameterOption('--' . \Symplify\EasyCodingStandard\ValueObject\Option::OUTPUT_FORMAT);
        return $outputFormat === \Symplify\EasyCodingStandard\Console\Output\ConsoleOutputFormatter::NAME;
    }
    /**
     * @return void
     */
    private function addExtraOptions(\ECSPrefix20210509\Symfony\Component\Console\Input\InputDefinition $inputDefinition)
    {
        $inputDefinition->addOption(new \ECSPrefix20210509\Symfony\Component\Console\Input\InputOption(\Symplify\EasyCodingStandard\ValueObject\Option::XDEBUG, null, \ECSPrefix20210509\Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Allow running xdebug'));
        $inputDefinition->addOption(new \ECSPrefix20210509\Symfony\Component\Console\Input\InputOption(\Symplify\EasyCodingStandard\ValueObject\Option::DEBUG, null, \ECSPrefix20210509\Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Run in debug mode (alias for "-vvv")'));
    }
    /**
     * @return string
     */
    private function resolveEasyCodingStandardVersion()
    {
        $installedRawData = \ECSPrefix20210509\Composer\InstalledVersions::getRawData();
        $ecsPackageData = isset($installedRawData['versions']['symplify/easy-coding-standard']) ? $installedRawData['versions']['symplify/easy-coding-standard'] : null;
        if ($ecsPackageData === null) {
            return 'Unknown';
        }
        if (isset($ecsPackageData['replaced'])) {
            return 'replaced@' . $ecsPackageData['replaced'][0];
        }
        if ($ecsPackageData['version'] === 'dev-main') {
            return 'dev-main@' . \ECSPrefix20210509\Nette\Utils\Strings::substring($ecsPackageData['reference'], 0, 7);
        }
        return $ecsPackageData['version'];
    }
}
