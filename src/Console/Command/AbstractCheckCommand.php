<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Console\Command;

use ECSPrefix20211002\Symfony\Component\Console\Input\InputArgument;
use ECSPrefix20211002\Symfony\Component\Console\Input\InputOption;
use ECSPrefix20211002\Symfony\Contracts\Service\Attribute\Required;
use Symplify\EasyCodingStandard\Application\EasyCodingStandardApplication;
use Symplify\EasyCodingStandard\Configuration\ConfigurationFactory;
use Symplify\EasyCodingStandard\Console\Output\ConsoleOutputFormatter;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Guard\LoadedCheckersGuard;
use Symplify\EasyCodingStandard\ValueObject\Option;
use ECSPrefix20211002\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
abstract class AbstractCheckCommand extends \ECSPrefix20211002\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand
{
    /**
     * @var \Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle
     */
    protected $easyCodingStandardStyle;
    /**
     * @var \Symplify\EasyCodingStandard\Application\EasyCodingStandardApplication
     */
    protected $easyCodingStandardApplication;
    /**
     * @var \Symplify\EasyCodingStandard\Guard\LoadedCheckersGuard
     */
    protected $loadedCheckersGuard;
    /**
     * @var \Symplify\EasyCodingStandard\Configuration\ConfigurationFactory
     */
    protected $configurationFactory;
    /**
     * @required
     */
    public function autowireAbstractCheckCommand(\Symplify\EasyCodingStandard\Configuration\ConfigurationFactory $configurationFactory, \Symplify\EasyCodingStandard\Application\EasyCodingStandardApplication $easyCodingStandardApplication, \Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle $easyCodingStandardStyle, \Symplify\EasyCodingStandard\Guard\LoadedCheckersGuard $loadedCheckersGuard) : void
    {
        $this->configurationFactory = $configurationFactory;
        $this->easyCodingStandardApplication = $easyCodingStandardApplication;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->loadedCheckersGuard = $loadedCheckersGuard;
    }
    protected function configure() : void
    {
        $this->addArgument(
            \Symplify\EasyCodingStandard\ValueObject\Option::PATHS,
            // optional is on purpose here, since path from ecs.php can se ubsed
            \ECSPrefix20211002\Symfony\Component\Console\Input\InputArgument::OPTIONAL | \ECSPrefix20211002\Symfony\Component\Console\Input\InputArgument::IS_ARRAY,
            'The path(s) to be checked.'
        );
        $this->addOption(\Symplify\EasyCodingStandard\ValueObject\Option::FIX, null, null, 'Fix found violations.');
        $this->addOption(\Symplify\EasyCodingStandard\ValueObject\Option::CLEAR_CACHE, null, null, 'Clear cache for already checked files.');
        $this->addOption(\Symplify\EasyCodingStandard\ValueObject\Option::NO_PROGRESS_BAR, null, \ECSPrefix20211002\Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Hide progress bar. Useful e.g. for nicer CI output.');
        $this->addOption(\Symplify\EasyCodingStandard\ValueObject\Option::NO_ERROR_TABLE, null, \ECSPrefix20211002\Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Hide error table. Useful e.g. for fast check of error count.');
        $this->addOption(\Symplify\EasyCodingStandard\ValueObject\Option::OUTPUT_FORMAT, null, \ECSPrefix20211002\Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'Select output format', \Symplify\EasyCodingStandard\Console\Output\ConsoleOutputFormatter::NAME);
        $this->addOption(\Symplify\EasyCodingStandard\ValueObject\Option::MATCH_GIT_DIFF, null, \ECSPrefix20211002\Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Execute only on file(s) matching the git diff.');
        $this->addOption(\Symplify\EasyCodingStandard\ValueObject\Option::PARALLEL_PORT, null, \ECSPrefix20211002\Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED);
        $this->addOption(\Symplify\EasyCodingStandard\ValueObject\Option::PARALLEL_IDENTIFIER, null, \ECSPrefix20211002\Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED);
    }
}
