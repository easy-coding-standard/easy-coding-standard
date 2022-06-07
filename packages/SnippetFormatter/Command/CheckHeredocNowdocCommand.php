<?php

declare (strict_types=1);
namespace ECSPrefix20220607\Symplify\EasyCodingStandard\SnippetFormatter\Command;

use ECSPrefix20220607\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20220607\Symfony\Component\Console\Output\OutputInterface;
use ECSPrefix20220607\Symplify\EasyCodingStandard\Console\Command\AbstractCheckCommand;
use ECSPrefix20220607\Symplify\EasyCodingStandard\SnippetFormatter\Application\SnippetFormatterApplication;
use ECSPrefix20220607\Symplify\EasyCodingStandard\SnippetFormatter\ValueObject\SnippetKind;
use ECSPrefix20220607\Symplify\EasyCodingStandard\SnippetFormatter\ValueObject\SnippetPattern;
use ECSPrefix20220607\Symplify\PackageBuilder\Console\Command\CommandNaming;
final class CheckHeredocNowdocCommand extends AbstractCheckCommand
{
    /**
     * @var \Symplify\EasyCodingStandard\SnippetFormatter\Application\SnippetFormatterApplication
     */
    private $snippetFormatterApplication;
    public function __construct(SnippetFormatterApplication $snippetFormatterApplication)
    {
        $this->snippetFormatterApplication = $snippetFormatterApplication;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Format Heredoc/Nowdoc PHP snippets in PHP files');
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
        return $this->snippetFormatterApplication->processFileInfosWithSnippetPattern($configuration, $phpFileInfos, SnippetPattern::HERENOWDOC_SNIPPET_REGEX, SnippetKind::HERE_NOW_DOC);
    }
}
