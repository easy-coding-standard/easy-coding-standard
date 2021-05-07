<?php

namespace Symplify\EasyCodingStandard\SnippetFormatter\Command;

use ECSPrefix20210507\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20210507\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Console\Command\AbstractCheckCommand;
use Symplify\EasyCodingStandard\SnippetFormatter\Application\SnippetFormatterApplication;
use Symplify\EasyCodingStandard\SnippetFormatter\ValueObject\SnippetPattern;
final class CheckMarkdownCommand extends AbstractCheckCommand
{
    /**
     * @var SnippetFormatterApplication
     */
    private $snippetFormatterApplication;
    /**
     * @param \Symplify\EasyCodingStandard\SnippetFormatter\Application\SnippetFormatterApplication $snippetFormatterApplication
     */
    public function __construct($snippetFormatterApplication)
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
     * @param \ECSPrefix20210507\Symfony\Component\Console\Input\InputInterface $input
     * @param \ECSPrefix20210507\Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    protected function execute($input, $output)
    {
        $this->configuration->resolveFromInput($input);
        $sources = $this->configuration->getSources();
        $phpFileInfos = $this->smartFinder->find($sources, '*.php', ['Fixture']);
        return $this->snippetFormatterApplication->processFileInfosWithSnippetPattern($this->configuration, $phpFileInfos, SnippetPattern::MARKDOWN_PHP_SNIPPET_REGEX, 'markdown');
    }
}
