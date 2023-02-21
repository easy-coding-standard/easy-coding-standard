<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Console\Command;

use ECSPrefix202302\Symfony\Component\Console\Command\Command;
use ECSPrefix202302\Symfony\Component\Console\Input\InputArgument;
use ECSPrefix202302\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix202302\Symfony\Component\Console\Output\OutputInterface;
use ECSPrefix202302\Symfony\Component\Console\Style\SymfonyStyle;
final class CheckMarkdownCommand extends Command
{
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('check-markdown');
        $this->setDescription('[DEPRECATED] Check markdown files');
        $this->addArgument('paths', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Paths to check');
        parent::configure();
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $this->symfonyStyle->warning('This command is deprecated. Use "ecs check" or direct php-parser printer instead.');
        return self::FAILURE;
    }
}
