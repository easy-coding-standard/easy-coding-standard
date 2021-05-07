<?php

namespace Symplify\EasyCodingStandard\SnippetFormatter\Reporter;

use ECSPrefix20210507\Symfony\Component\Console\Style\SymfonyStyle;
final class SnippetReporter
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @param \ECSPrefix20210507\Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle
     */
    public function __construct($symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }
    /**
     * @param string[] $sources
     * @return void
     */
    public function reportNoFilesFound(array $sources)
    {
        $message = \sprintf('No files found in "%s" paths.%sCheck CLI arguments or "Option::PATHS" parameter in "ecs.php" config file', \implode('", ', $sources), \PHP_EOL);
        $this->symfonyStyle->warning($message);
    }
}
