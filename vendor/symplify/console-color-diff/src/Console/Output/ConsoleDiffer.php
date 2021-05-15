<?php

namespace ECSPrefix20210515\Symplify\ConsoleColorDiff\Console\Output;

use ECSPrefix20210515\SebastianBergmann\Diff\Differ;
use ECSPrefix20210515\Symplify\ConsoleColorDiff\Console\Formatter\ColorConsoleDiffFormatter;
final class ConsoleDiffer
{
    /**
     * @var Differ
     */
    private $differ;
    /**
     * @var ColorConsoleDiffFormatter
     */
    private $colorConsoleDiffFormatter;
    public function __construct(\ECSPrefix20210515\SebastianBergmann\Diff\Differ $differ, \ECSPrefix20210515\Symplify\ConsoleColorDiff\Console\Formatter\ColorConsoleDiffFormatter $colorConsoleDiffFormatter)
    {
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
}
