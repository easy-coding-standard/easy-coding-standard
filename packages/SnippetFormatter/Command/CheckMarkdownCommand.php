<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\SnippetFormatter\Command;

use ECSPrefix202209\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix202209\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Console\Command\AbstractCheckCommand;
use Symplify\EasyCodingStandard\SnippetFormatter\Application\MarkdownSnippetFormatterApplication;
final class CheckMarkdownCommand extends AbstractCheckCommand
{
    /**
     * @var \Symplify\EasyCodingStandard\SnippetFormatter\Application\MarkdownSnippetFormatterApplication
     */
    private $markdownSnippetFormatterApplication;
    public function __construct(MarkdownSnippetFormatterApplication $markdownSnippetFormatterApplication)
    {
        $this->markdownSnippetFormatterApplication = $markdownSnippetFormatterApplication;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('check-markdown');
        $this->setDescription('Format Markdown PHP code');
        parent::configure();
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        if (!$this->loadedCheckersGuard->areSomeCheckersRegistered()) {
            $this->loadedCheckersGuard->report();
            return self::FAILURE;
        }
        $configuration = $this->configurationFactory->createFromInput($input);
        $phpFileInfos = $this->smartFinder->find($configuration->getSources(), '*.php', ['Fixture']);
        return $this->markdownSnippetFormatterApplication->processFileInfosWithSnippetPattern($configuration, $phpFileInfos);
    }
}
