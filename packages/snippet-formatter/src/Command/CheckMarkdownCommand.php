<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SnippetFormatter\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Console\Command\AbstractCheckCommand;
use Symplify\EasyCodingStandard\SnippetFormatter\Application\SnippetFormatterApplication;
use Symplify\EasyCodingStandard\SnippetFormatter\ValueObject\SnippetPattern;

final class CheckMarkdownCommand extends AbstractCheckCommand
{
    /**
     * @var SnippetFormatterApplication
     */
    private $snippetFormatterApplication;

    public function __construct(SnippetFormatterApplication $snippetFormatterApplication)
    {
        $this->snippetFormatterApplication = $snippetFormatterApplication;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Format Markdown PHP code');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->configuration->resolveFromInput($input);
        $sources = $this->configuration->getSources();
        $phpFileInfos = $this->smartFinder->find($sources, '*.php', ['Fixture']);

        return $this->snippetFormatterApplication->processFileInfosWithSnippetPattern(
            $this->configuration,
            $phpFileInfos,
            SnippetPattern::MARKDOWN_PHP_SNIPPET_REGEX,
            'markdown'
        );
    }
}
