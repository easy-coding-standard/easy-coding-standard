<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SnippetFormatter\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\SnippetFormatter\ValueObject\SnippetPattern;
use Symplify\PackageBuilder\Console\Command\CommandNaming;

final class CheckMarkdownCommand extends AbstractSnippetFormatterCommand
{
    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Format Markdown PHP code');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return $this->doExecuteSnippetFormatterWithFileNamesAndSnippetPattern(
            $input,
            '*.md',
            SnippetPattern::MARKDOWN_PHP_SNIPPET_PATTERN
        );
    }
}
