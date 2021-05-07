<?php

namespace Symplify\EasyCodingStandard\Console\Command;

use ECSPrefix20210507\Symfony\Component\Console\Input\InputArgument;
use ECSPrefix20210507\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20210507\Symfony\Component\Console\Input\InputOption;
use ECSPrefix20210507\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Application\EasyCodingStandardApplication;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Console\Output\ConsoleOutputFormatter;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Guard\LoadedCheckersGuard;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
abstract class AbstractCheckCommand extends \Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand
{
    /**
     * @var Configuration
     */
    protected $configuration;
    /**
     * @var EasyCodingStandardStyle
     */
    protected $easyCodingStandardStyle;
    /**
     * @var EasyCodingStandardApplication
     */
    protected $easyCodingStandardApplication;
    /**
     * @var LoadedCheckersGuard
     */
    private $loadedCheckersGuard;
    /**
     * @required
     * @return void
     * @param \Symplify\EasyCodingStandard\Configuration\Configuration $configuration
     * @param \Symplify\EasyCodingStandard\Application\EasyCodingStandardApplication $easyCodingStandardApplication
     * @param \Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle $easyCodingStandardStyle
     * @param \Symplify\EasyCodingStandard\Guard\LoadedCheckersGuard $loadedCheckersGuard
     */
    public function autowireAbstractCheckCommand($configuration, $easyCodingStandardApplication, $easyCodingStandardStyle, $loadedCheckersGuard)
    {
        $this->configuration = $configuration;
        $this->easyCodingStandardApplication = $easyCodingStandardApplication;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->loadedCheckersGuard = $loadedCheckersGuard;
    }
    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument(
            \Symplify\EasyCodingStandard\ValueObject\Option::PATHS,
            // optional is on purpose here, since path from ecs.php can se ubsed
            \ECSPrefix20210507\Symfony\Component\Console\Input\InputArgument::OPTIONAL | \ECSPrefix20210507\Symfony\Component\Console\Input\InputArgument::IS_ARRAY,
            'The path(s) to be checked.'
        );
        $this->addOption(\Symplify\EasyCodingStandard\ValueObject\Option::FIX, null, null, 'Fix found violations.');
        $this->addOption(\Symplify\EasyCodingStandard\ValueObject\Option::CLEAR_CACHE, null, null, 'Clear cache for already checked files.');
        $this->addOption(\Symplify\EasyCodingStandard\ValueObject\Option::NO_PROGRESS_BAR, null, \ECSPrefix20210507\Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Hide progress bar. Useful e.g. for nicer CI output.');
        $this->addOption(\Symplify\EasyCodingStandard\ValueObject\Option::NO_ERROR_TABLE, null, \ECSPrefix20210507\Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Hide error table. Useful e.g. for fast check of error count.');
        $this->addOption(\Symplify\EasyCodingStandard\ValueObject\Option::OUTPUT_FORMAT, null, \ECSPrefix20210507\Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'Select output format', \Symplify\EasyCodingStandard\Console\Output\ConsoleOutputFormatter::NAME);
        $this->addOption(\Symplify\EasyCodingStandard\ValueObject\Option::MATCH_GIT_DIFF, null, \ECSPrefix20210507\Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Execute only on file(s) matching the git diff.');
    }
    /**
     * @param \ECSPrefix20210507\Symfony\Component\Console\Input\InputInterface $input
     * @param \ECSPrefix20210507\Symfony\Component\Console\Output\OutputInterface $output
     * @return void
     */
    protected function initialize($input, $output)
    {
        $this->loadedCheckersGuard->ensureSomeCheckersAreRegistered();
    }
}
