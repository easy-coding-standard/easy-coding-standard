<?php

namespace Symplify\ConsoleColorDiff\Console\Output;

use ECSPrefix20210507\SebastianBergmann\Diff\Differ;
use ECSPrefix20210507\Symfony\Component\Console\Style\SymfonyStyle;
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
    /**
     * @param \ECSPrefix20210507\Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle
     * @param \ECSPrefix20210507\SebastianBergmann\Diff\Differ $differ
     * @param \Symplify\ConsoleColorDiff\Console\Formatter\ColorConsoleDiffFormatter $colorConsoleDiffFormatter
     */
    public function __construct($symfonyStyle, $differ, $colorConsoleDiffFormatter)
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
        $formattedDiff = $this->diff($old, $new);
        $this->symfonyStyle->writeln($formattedDiff);
    }
}
