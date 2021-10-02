<?php

declare (strict_types=1);
namespace ECSPrefix20211002\Symplify\ConsoleColorDiff\Console\Output;

use ECSPrefix20211002\SebastianBergmann\Diff\Differ;
use ECSPrefix20211002\Symplify\ConsoleColorDiff\Console\Formatter\ColorConsoleDiffFormatter;
final class ConsoleDiffer
{
    /**
     * @var \SebastianBergmann\Diff\Differ
     */
    private $differ;
    /**
     * @var \Symplify\ConsoleColorDiff\Console\Formatter\ColorConsoleDiffFormatter
     */
    private $colorConsoleDiffFormatter;
    public function __construct(\ECSPrefix20211002\SebastianBergmann\Diff\Differ $differ, \ECSPrefix20211002\Symplify\ConsoleColorDiff\Console\Formatter\ColorConsoleDiffFormatter $colorConsoleDiffFormatter)
    {
        $this->differ = $differ;
        $this->colorConsoleDiffFormatter = $colorConsoleDiffFormatter;
    }
    public function diff(string $old, string $new) : string
    {
        $diff = $this->differ->diff($old, $new);
        return $this->colorConsoleDiffFormatter->format($diff);
    }
}
