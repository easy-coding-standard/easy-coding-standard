<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Console\Command;

use ECSPrefix202302\Symfony\Component\Console\Input\InputArgument;
use ECSPrefix202302\Symfony\Component\Console\Input\InputOption;
use ECSPrefix202302\Symfony\Contracts\Service\Attribute\Required;
use Symplify\EasyCodingStandard\Configuration\ConfigurationFactory;
use Symplify\EasyCodingStandard\Console\Output\ConsoleOutputFormatter;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\ValueObject\Option;
use ECSPrefix202302\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
abstract class AbstractCheckCommand extends AbstractSymplifyCommand
{
    /**
     * @var \Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle
     */
    protected $easyCodingStandardStyle;
    /**
     * @var \Symplify\EasyCodingStandard\Configuration\ConfigurationFactory
     */
    protected $configurationFactory;
    /**
     * @required
     */
    public function autowireAbstractCheckCommand(ConfigurationFactory $configurationFactory, EasyCodingStandardStyle $easyCodingStandardStyle) : void
    {
        $this->configurationFactory = $configurationFactory;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
    }
    protected function configure() : void
    {
        $this->addArgument(
            Option::PATHS,
            // optional is on purpose here, since path from ecs.php can se used
            InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
            'The path(s) to be checked.'
        );
        $this->addOption(Option::FIX, null, null, 'Fix found violations.');
        $this->addOption(Option::CLEAR_CACHE, null, null, 'Clear cache for already checked files.');
        $this->addOption(Option::NO_PROGRESS_BAR, null, InputOption::VALUE_NONE, 'Hide progress bar. Useful e.g. for nicer CI output.');
        $this->addOption(Option::NO_ERROR_TABLE, null, InputOption::VALUE_NONE, 'Hide error table. Useful e.g. for fast check of error count.');
        $this->addOption(Option::OUTPUT_FORMAT, null, InputOption::VALUE_REQUIRED, 'Select output format', ConsoleOutputFormatter::NAME);
        $this->addOption(Option::MEMORY_LIMIT, null, InputOption::VALUE_REQUIRED, 'Memory limit for check');
        $this->addOption(Option::PARALLEL_PORT, null, InputOption::VALUE_REQUIRED);
        $this->addOption(Option::PARALLEL_IDENTIFIER, null, InputOption::VALUE_REQUIRED);
    }
}
