<?php

namespace Symplify\ConsoleColorDiff\Console\Output;

use ECSPrefix20210510\SebastianBergmann\Diff\Differ;
use ECSPrefix20210510\Symfony\Component\Console\Style\SymfonyStyle;
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
    public function __construct(\ECSPrefix20210510\Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle, \ECSPrefix20210510\SebastianBergmann\Diff\Differ $differ, \Symplify\ConsoleColorDiff\Console\Formatter\ColorConsoleDiffFormatter $colorConsoleDiffFormatter)
    {
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
