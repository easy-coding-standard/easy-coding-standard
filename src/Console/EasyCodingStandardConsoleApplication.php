<?php

declare (strict_types=1);
namespace ECSPrefix20220607\Symplify\EasyCodingStandard\Console;

use ECSPrefix20220607\Composer\XdebugHandler\XdebugHandler;
use ECSPrefix20220607\Symfony\Component\Console\Application;
use ECSPrefix20220607\Symfony\Component\Console\Command\Command;
use ECSPrefix20220607\Symfony\Component\Console\Input\InputDefinition;
use ECSPrefix20220607\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20220607\Symfony\Component\Console\Input\InputOption;
use ECSPrefix20220607\Symfony\Component\Console\Output\OutputInterface;
use ECSPrefix20220607\Symplify\EasyCodingStandard\Application\Version\StaticVersionResolver;
use ECSPrefix20220607\Symplify\EasyCodingStandard\Console\Command\CheckCommand;
use ECSPrefix20220607\Symplify\EasyCodingStandard\Console\Output\ConsoleOutputFormatter;
use ECSPrefix20220607\Symplify\EasyCodingStandard\ValueObject\Option;
use ECSPrefix20220607\Symplify\PackageBuilder\Console\Command\CommandNaming;
final class EasyCodingStandardConsoleApplication extends Application
{
    /**
     * @param Command[] $commands
     */
    public function __construct(array $commands)
    {
        parent::__construct('EasyCodingStandard', StaticVersionResolver::PACKAGE_VERSION);
        // @see https://tomasvotruba.com/blog/2020/10/26/the-bullet-proof-symfony-command-naming/
        $this->addCommands($commands);
        $this->setDefaultCommand(CommandNaming::classToName(CheckCommand::class));
    }
    public function doRun(InputInterface $input, OutputInterface $output) : int
    {
        // @fixes https://github.com/rectorphp/rector/issues/2205
        $isXdebugAllowed = $input->hasParameterOption('--xdebug');
        if (!$isXdebugAllowed && !\defined('ECSPrefix20220607\\PHPUNIT_COMPOSER_INSTALL')) {
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
