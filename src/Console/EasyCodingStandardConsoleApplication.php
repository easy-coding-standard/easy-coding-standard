<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Console;

use ECSPrefix20211002\Composer\XdebugHandler\XdebugHandler;
use ECSPrefix20211002\Symfony\Component\Console\Application;
use ECSPrefix20211002\Symfony\Component\Console\Command\Command;
use ECSPrefix20211002\Symfony\Component\Console\Input\InputDefinition;
use ECSPrefix20211002\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20211002\Symfony\Component\Console\Input\InputOption;
use ECSPrefix20211002\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Application\Version\StaticVersionResolver;
use Symplify\EasyCodingStandard\Console\Command\CheckCommand;
use Symplify\EasyCodingStandard\Console\Output\ConsoleOutputFormatter;
use Symplify\EasyCodingStandard\ValueObject\Option;
use ECSPrefix20211002\Symplify\PackageBuilder\Console\Command\CommandNaming;
final class EasyCodingStandardConsoleApplication extends \ECSPrefix20211002\Symfony\Component\Console\Application
{
    /**
     * @param Command[] $commands
     */
    public function __construct(array $commands)
    {
        parent::__construct('EasyCodingStandard', \Symplify\EasyCodingStandard\Application\Version\StaticVersionResolver::PACKAGE_VERSION);
        // @see https://tomasvotruba.com/blog/2020/10/26/the-bullet-proof-symfony-command-naming/
        $commandNaming = new \ECSPrefix20211002\Symplify\PackageBuilder\Console\Command\CommandNaming();
        foreach ($commands as $command) {
            $commandName = $commandNaming->resolveFromCommand($command);
            $command->setName($commandName);
            $this->add($command);
        }
        $this->setDefaultCommand(\ECSPrefix20211002\Symplify\PackageBuilder\Console\Command\CommandNaming::classToName(\Symplify\EasyCodingStandard\Console\Command\CheckCommand::class));
    }
    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function doRun($input, $output) : int
    {
        // @fixes https://github.com/rectorphp/rector/issues/2205
        $isXdebugAllowed = $input->hasParameterOption('--xdebug');
        if (!$isXdebugAllowed && !\defined('PHPUNIT_COMPOSER_INSTALL')) {
            $xdebugHandler = new \ECSPrefix20211002\Composer\XdebugHandler\XdebugHandler('ecs');
            $xdebugHandler->check();
            unset($xdebugHandler);
        }
        // skip in this case, since generate content must be clear from meta-info
        if ($this->shouldPrintMetaInformation($input)) {
            $output->writeln($this->getLongVersion());
        }
        return parent::doRun($input, $output);
    }
    protected function getDefaultInputDefinition() : \ECSPrefix20211002\Symfony\Component\Console\Input\InputDefinition
    {
        $inputDefinition = parent::getDefaultInputDefinition();
        $this->addExtraOptions($inputDefinition);
        return $inputDefinition;
    }
    private function shouldPrintMetaInformation(\ECSPrefix20211002\Symfony\Component\Console\Input\InputInterface $input) : bool
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
    private function addExtraOptions(\ECSPrefix20211002\Symfony\Component\Console\Input\InputDefinition $inputDefinition) : void
    {
        $inputDefinition->addOption(new \ECSPrefix20211002\Symfony\Component\Console\Input\InputOption(\Symplify\EasyCodingStandard\ValueObject\Option::XDEBUG, null, \ECSPrefix20211002\Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Allow running xdebug'));
        $inputDefinition->addOption(new \ECSPrefix20211002\Symfony\Component\Console\Input\InputOption(\Symplify\EasyCodingStandard\ValueObject\Option::DEBUG, null, \ECSPrefix20211002\Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Run in debug mode (alias for "-vvv")'));
    }
}
