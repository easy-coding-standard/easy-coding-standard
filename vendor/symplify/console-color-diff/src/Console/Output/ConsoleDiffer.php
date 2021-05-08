<?php

namespace Symplify\ConsoleColorDiff\Console\Output;

use ECSPrefix20210508\SebastianBergmann\Diff\Differ;
use ECSPrefix20210508\Symfony\Component\Console\Style\SymfonyStyle;
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
    public function __construct(\ECSPrefix20210508\Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle, \ECSPrefix20210508\SebastianBergmann\Diff\Differ $differ, \Symplify\ConsoleColorDiff\Console\Formatter\ColorConsoleDiffFormatter $colorConsoleDiffFormatter)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->differ = $differ;
        $this->colorConsoleDiffFormatter = $colorConsoleDiffFormatter;
    }
    /**
     * @param string $old
     */
    public function diff($old, string $new) : string
    {
        if (\is_object($old)) {
            $old = (string) $old;
        }
        $diff = $this->differ->diff($old, $new);
        return $this->colorConsoleDiffFormatter->format($diff);
    }
    /**
     * @return void
     * @param string $old
     */
    public function diffAndPrint($old, string $new)
    {
        if (\is_object($old)) {
            $old = (string) $old;
        }
        $formattedDiff = $this->diff($old, $new);
        $this->symfonyStyle->writeln($formattedDiff);
    }
}
