<?php

namespace Symplify\EasyCodingStandard\SnippetFormatter\Reporter;

use Symfony\Component\Console\Style\SymfonyStyle;

final class SnippetReporter
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }

    /**
     * @param string[] $sources
     * @return void
     */
    public function reportNoFilesFound(array $sources)
    {
        $message = sprintf(
            'No files found in "%s" paths.%sCheck CLI arguments or "Option::PATHS" parameter in "ecs.php" config file',
            implode('", ', $sources),
            PHP_EOL
        );

        $this->symfonyStyle->warning($message);
    }
}
