<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Console;

use ECSPrefix20220220\Composer\XdebugHandler\XdebugHandler;
use ECSPrefix20220220\Symfony\Component\Console\Application;
use ECSPrefix20220220\Symfony\Component\Console\Command\Command;
use ECSPrefix20220220\Symfony\Component\Console\Input\InputDefinition;
use ECSPrefix20220220\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20220220\Symfony\Component\Console\Input\InputOption;
use ECSPrefix20220220\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Application\Version\StaticVersionResolver;
use Symplify\EasyCodingStandard\Console\Command\CheckCommand;
use Symplify\EasyCodingStandard\Console\Output\ConsoleOutputFormatter;
use Symplify\EasyCodingStandard\ValueObject\Option;
use ECSPrefix20220220\Symplify\PackageBuilder\Console\Command\CommandNaming;
final class EasyCodingStandardConsoleApplication extends \ECSPrefix20220220\Symfony\Component\Console\Application
{
    /**
     * @param Command[] $commands
     */
    public function __construct(array $commands)
    {
        parent::__construct('EasyCodingStandard', \Symplify\EasyCodingStandard\Application\Version\StaticVersionResolver::PACKAGE_VERSION);
        // @see https://tomasvotruba.com/blog/2020/10/26/the-bullet-proof-symfony-command-naming/
        $this->addCommands($commands);
        $this->setDefaultCommand(\ECSPrefix20220220\Symplify\PackageBuilder\Console\Command\CommandNaming::classToName(\Symplify\EasyCodingStandard\Console\Command\CheckCommand::class));
    }
    public function doRun(\ECSPrefix20220220\Symfony\Component\Console\Input\InputInterface $input, \ECSPrefix20220220\Symfony\Component\Console\Output\OutputInterface $output) : int
    {
        // @fixes https://github.com/rectorphp/rector/issues/2205
        $isXdebugAllowed = $input->hasParameterOption('--xdebug');
        if (!$isXdebugAllowed && !\defined('PHPUNIT_COMPOSER_INSTALL')) {
            $xdebugHandler = new \ECSPrefix20220220\Composer\XdebugHandler\XdebugHandler('ecs');
            $xdebugHandler->check();
            unset($xdebugHandler);
        }
        // skip in this case, since generate content must be clear from meta-info
        if ($this->shouldPrintMetaInformation($input)) {
            $output->writeln($this->getLongVersion());
        }
        return parent::doRun($input, $output);
    }
    protected function getDefaultInputDefinition() : \ECSPrefix20220220\Symfony\Component\Console\Input\InputDefinition
    {
        $inputDefinition = parent::getDefaultInputDefinition();
        $this->addExtraOptions($inputDefinition);
        return $inputDefinition;
    }
    private function shouldPrintMetaInformation(\ECSPrefix20220220\Symfony\Component\Console\Input\InputInterface $input) : bool
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
    private function addExtraOptions(\ECSPrefix20220220\Symfony\Component\Console\Input\InputDefinition $inputDefinition) : void
    {
        $inputDefinition->addOption(new \ECSPrefix20220220\Symfony\Component\Console\Input\InputOption(\Symplify\EasyCodingStandard\ValueObject\Option::XDEBUG, null, \ECSPrefix20220220\Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Allow running xdebug'));
        $inputDefinition->addOption(new \ECSPrefix20220220\Symfony\Component\Console\Input\InputOption(\Symplify\EasyCodingStandard\ValueObject\Option::DEBUG, null, \ECSPrefix20220220\Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Run in debug mode (alias for "-vvv")'));
    }
}
