<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SnippetFormatter\Command;

use SplFileInfo;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Console\Command\AbstractCheckCommand;
use Symplify\EasyCodingStandard\SnippetFormatter\Application\MarkdownSnippetFormatterApplication;

final class CheckMarkdownCommand extends AbstractCheckCommand
{
    public function __construct(
        private readonly MarkdownSnippetFormatterApplication $markdownSnippetFormatterApplication
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('check-markdown');
        $this->setDescription('Format Markdown PHP code');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (! $this->loadedCheckersGuard->areSomeCheckersRegistered()) {
            $this->loadedCheckersGuard->report();
            return self::FAILURE;
        }

        $configuration = $this->configurationFactory->createFromInput($input);
        $phpPileInfos = $this->smartFinder->find($configuration->getSources(), '*.php', ['Fixture']);

        $filePaths = array_map(static fn (SplFileInfo $fileInfo): string => $fileInfo->getRealPath(), $phpPileInfos);

        return $this->markdownSnippetFormatterApplication->processFileInfosWithSnippetPattern(
            $configuration,
            $filePaths
        );
    }
}
