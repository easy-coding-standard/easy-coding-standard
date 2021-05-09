<?php

namespace Symplify\EasyCodingStandard\SnippetFormatter\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Console\Command\AbstractCheckCommand;
use Symplify\EasyCodingStandard\SnippetFormatter\Application\SnippetFormatterApplication;
use Symplify\EasyCodingStandard\SnippetFormatter\ValueObject\SnippetPattern;
use Symplify\PackageBuilder\Console\ShellCode;

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

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('Format Markdown PHP code');

        parent::configure();
    }

    /**
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->loadedCheckersGuard->areSomeCheckerRegistered() === false) {
            $this->loadedCheckersGuard->report();
            return ShellCode::ERROR;
        }

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
