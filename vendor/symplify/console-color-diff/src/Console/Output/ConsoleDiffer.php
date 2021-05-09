<?php

namespace Symplify\ConsoleColorDiff\Console\Output;

use SebastianBergmann\Diff\Differ;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\ConsoleColorDiff\Console\Formatter\ColorConsoleDiffFormatter;

final class ConsoleDiffer
{
    /**
     * @var Differ
     */
    private $differ;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var ColorConsoleDiffFormatter
     */
    private $colorConsoleDiffFormatter;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        Differ $differ,
        ColorConsoleDiffFormatter $colorConsoleDiffFormatter
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->differ = $differ;
        $this->colorConsoleDiffFormatter = $colorConsoleDiffFormatter;
    }

    /**
     * @param string $old
     * @param string $new
     * @return string
     */
    public function diff($old, $new)
    {
        $old = (string) $old;
        $new = (string) $new;
        $diff = $this->differ->diff($old, $new);
        return $this->colorConsoleDiffFormatter->format($diff);
    }

    /**
     * @return void
     * @param string $old
     * @param string $new
     */
    public function diffAndPrint($old, $new)
    {
        $old = (string) $old;
        $new = (string) $new;
        $formattedDiff = $this->diff($old, $new);
        $this->symfonyStyle->writeln($formattedDiff);
    }
}
