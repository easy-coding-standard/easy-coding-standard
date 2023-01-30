<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Console\Command;

use ECSPrefix202301\Symfony\Component\Console\Command\Command;
use ECSPrefix202301\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix202301\Symfony\Component\Console\Output\OutputInterface;
use ECSPrefix202301\Symfony\Component\Console\Style\SymfonyStyle;
use ECSPrefix202301\Symfony\Component\Filesystem\Filesystem;
final class InitCommand extends Command
{
    /**
     * @readonly
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    public function __construct(Filesystem $filesystem, SymfonyStyle $symfonyStyle)
    {
        $this->filesystem = $filesystem;
        $this->symfonyStyle = $symfonyStyle;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('init');
        $this->setDescription('Generate ecs.php configuration file');
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $doesConfigExists = $this->filesystem->exists(\getcwd() . '/ecs.php');
        // @todo figure out a better versoin
        if (!$doesConfigExists) {
            $this->filesystem->copy(__DIR__ . '/../../../templates/ecs.php.dist', \getcwd() . '/ecs.php');
            $this->symfonyStyle->success('ecs.php config file has been generated successfully');
        } else {
            $this->symfonyStyle->warning('The "ecs.php" configuration file already exists');
        }
        return self::SUCCESS;
    }
}
