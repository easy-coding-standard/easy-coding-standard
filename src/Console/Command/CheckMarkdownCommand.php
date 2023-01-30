<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class CheckMarkdownCommand extends Command
{
    public function __construct(
        private readonly SymfonyStyle $symfonyStyle
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('check-markdown');
        $this->setDescription('[DEPRECATED] Check markdown files');

        $this->addArgument('paths', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Paths to check');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->symfonyStyle->warning(
            'This command is deprecated. Use "ecs check" or direct php-parser printer instead.'
        );

        return self::FAILURE;
    }
}
