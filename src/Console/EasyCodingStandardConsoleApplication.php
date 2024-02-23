<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Console;

use ECSPrefix202402\Composer\XdebugHandler\XdebugHandler;
use ECSPrefix202402\Symfony\Component\Console\Application;
use ECSPrefix202402\Symfony\Component\Console\Input\InputDefinition;
use ECSPrefix202402\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix202402\Symfony\Component\Console\Input\InputOption;
use ECSPrefix202402\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Application\Version\StaticVersionResolver;
use Symplify\EasyCodingStandard\Console\Command\CheckCommand;
use Symplify\EasyCodingStandard\Console\Command\ListCheckersCommand;
use Symplify\EasyCodingStandard\Console\Command\ScriptsCommand;
use Symplify\EasyCodingStandard\Console\Command\WorkerCommand;
use Symplify\EasyCodingStandard\Console\Output\ConsoleOutputFormatter;
use Symplify\EasyCodingStandard\ValueObject\Option;
final class EasyCodingStandardConsoleApplication extends Application
{
    public function __construct(CheckCommand $checkCommand, WorkerCommand $workerCommand, ScriptsCommand $scriptsCommand, ListCheckersCommand $listCheckersCommand)
    {
        parent::__construct('EasyCodingStandard', StaticVersionResolver::PACKAGE_VERSION);
        // used only internally, not needed to be public
        $workerCommand->setHidden();
        $this->add($checkCommand);
        $this->add($workerCommand);
        $this->add($scriptsCommand);
        $this->add($listCheckersCommand);
        $this->get('completion')->setHidden();
        $this->get('help')->setHidden();
        $this->get('list')->setHidden();
        $this->setDefaultCommand('check');
    }
    public function doRun(InputInterface $input, OutputInterface $output) : int
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
    protected function getDefaultInputDefinition() : InputDefinition
    {
        $inputDefinition = parent::getDefaultInputDefinition();
        $this->addExtraOptions($inputDefinition);
        return $inputDefinition;
    }
    private function shouldPrintMetaInformation(InputInterface $input) : bool
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
    private function addExtraOptions(InputDefinition $inputDefinition) : void
    {
        $inputDefinition->addOption(new InputOption(Option::XDEBUG, null, InputOption::VALUE_NONE, 'Allow running xdebug'));
        $inputDefinition->addOption(new InputOption(Option::DEBUG, null, InputOption::VALUE_NONE, 'Run in debug mode (alias for "-vvv")'));
    }
}
