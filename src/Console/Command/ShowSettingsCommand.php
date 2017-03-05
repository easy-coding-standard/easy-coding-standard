<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Configuration\ConfigurationFileLoader;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;

final class ShowSettingsCommand extends Command
{
    /**
     * @var ConfigurationFileLoader
     */
    private $configurationFileLoader;

    /**
     * @var EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;

    public function __construct(
        ConfigurationFileLoader $configurationFileLoader,
        EasyCodingStandardStyle $easyCodingStandardStyle
    ) {
        parent::__construct();

        $this->configurationFileLoader = $configurationFileLoader;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
    }

    protected function configure(): void
    {
        $this->setName('show-settings');
        $this->setDescription('Show used fixers and sniffs and ignored files and sniffs.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $configuration = $this->configurationFileLoader->load();

        $this->easyCodingStandardStyle->title('Settings for EasyCodingStandard');
        $this->easyCodingStandardStyle->newLine();

        // @todo: display all fixers and sniffers with configuration: $configuration

        return 0;
    }
}
