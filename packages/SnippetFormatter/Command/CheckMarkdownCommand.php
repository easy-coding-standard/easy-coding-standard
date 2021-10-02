<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\SnippetFormatter\Command;

use ECSPrefix20211002\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20211002\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Console\Command\AbstractCheckCommand;
use Symplify\EasyCodingStandard\SnippetFormatter\Application\SnippetFormatterApplication;
use Symplify\EasyCodingStandard\SnippetFormatter\ValueObject\SnippetKind;
use Symplify\EasyCodingStandard\SnippetFormatter\ValueObject\SnippetPattern;
final class CheckMarkdownCommand extends \Symplify\EasyCodingStandard\Console\Command\AbstractCheckCommand
{
    /**
     * @var \Symplify\EasyCodingStandard\SnippetFormatter\Application\SnippetFormatterApplication
     */
    private $snippetFormatterApplication;
    public function __construct(\Symplify\EasyCodingStandard\SnippetFormatter\Application\SnippetFormatterApplication $snippetFormatterApplication)
    {
        $this->snippetFormatterApplication = $snippetFormatterApplication;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setDescription('Format Markdown PHP code');
        parent::configure();
    }
    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute($input, $output) : int
    {
        if (!$this->loadedCheckersGuard->areSomeCheckersRegistered()) {
            $this->loadedCheckersGuard->report();
            return self::FAILURE;
        }
        $configuration = $this->configurationFactory->createFromInput($input);
        $phpFileInfos = $this->smartFinder->find($configuration->getSources(), '*.php', ['Fixture']);
        return $this->snippetFormatterApplication->processFileInfosWithSnippetPattern($configuration, $phpFileInfos, \Symplify\EasyCodingStandard\SnippetFormatter\ValueObject\SnippetPattern::MARKDOWN_PHP_SNIPPET_REGEX, \Symplify\EasyCodingStandard\SnippetFormatter\ValueObject\SnippetKind::MARKDOWN);
    }
}
