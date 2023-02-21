<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Console\Command;

use ECSPrefix202302\Symfony\Component\Console\Command\Command;
use ECSPrefix202302\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix202302\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Configuration\ConfigInitializer;
/**
 * @deprecated Built-in the check command itself to easy the process.
 */
final class InitCommand extends Command
{
    /**
     * @readonly
     * @var \Symplify\EasyCodingStandard\Configuration\ConfigInitializer
     */
    private $configInitializer;
    public function __construct(ConfigInitializer $configInitializer)
    {
        $this->configInitializer = $configInitializer;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('init');
        $this->setDescription('[DEPRECATED] Generate ecs.php configuration file');
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $this->configInitializer->createConfig(\getcwd());
        return self::SUCCESS;
    }
}
