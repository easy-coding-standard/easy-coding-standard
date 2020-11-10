<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SnippetFormatter\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\SnippetFormatter\ValueObject\SnippetPattern;

final class CheckHeredocNowdocCommand extends AbstractSnippetFormatterCommand
{
    protected function configure(): void
    {
        $this->setDescription('Format Heredoc/Nowdoc PHP snippets in PHP files');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return $this->doExecuteSnippetFormatterWithFileNamesAndSnippetPattern(
            $input,
            '*.php',
            SnippetPattern::HERENOWDOC_SNIPPET_REGEX,
            'heredocnowdox'
        );
    }
}
