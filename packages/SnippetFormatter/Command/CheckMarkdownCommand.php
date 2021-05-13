<?php

namespace Symplify\EasyCodingStandard\SnippetFormatter\Command;

use ECSPrefix20210513\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20210513\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Console\Command\AbstractCheckCommand;
use Symplify\EasyCodingStandard\SnippetFormatter\Application\SnippetFormatterApplication;
use Symplify\EasyCodingStandard\SnippetFormatter\ValueObject\SnippetPattern;
use Symplify\PackageBuilder\Console\ShellCode;
final class CheckMarkdownCommand extends \Symplify\EasyCodingStandard\Console\Command\AbstractCheckCommand
{
    /**
     * @var SnippetFormatterApplication
     */
    private $snippetFormatterApplication;
    public function __construct(\Symplify\EasyCodingStandard\SnippetFormatter\Application\SnippetFormatterApplication $snippetFormatterApplication)
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
    protected function execute(\ECSPrefix20210513\Symfony\Component\Console\Input\InputInterface $input, \ECSPrefix20210513\Symfony\Component\Console\Output\OutputInterface $output)
    {
        if ($this->loadedCheckersGuard->areSomeCheckerRegistered() === \false) {
            $this->loadedCheckersGuard->report();
            return \Symplify\PackageBuilder\Console\ShellCode::ERROR;
        }
        $this->configuration->resolveFromInput($input);
        $sources = $this->configuration->getSources();
        $phpFileInfos = $this->smartFinder->find($sources, '*.php', ['Fixture']);
        return $this->snippetFormatterApplication->processFileInfosWithSnippetPattern($this->configuration, $phpFileInfos, \Symplify\EasyCodingStandard\SnippetFormatter\ValueObject\SnippetPattern::MARKDOWN_PHP_SNIPPET_REGEX, 'markdown');
    }
}
