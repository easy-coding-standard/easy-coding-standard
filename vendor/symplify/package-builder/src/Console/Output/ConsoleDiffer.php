<?php

declare (strict_types=1);
namespace ECSPrefix202306\Symplify\PackageBuilder\Console\Output;

use ECSPrefix202306\SebastianBergmann\Diff\Differ;
use ECSPrefix202306\Symplify\PackageBuilder\Console\Formatter\ColorConsoleDiffFormatter;
/**
 * @api
 */
final class ConsoleDiffer
{
    /**
     * @readonly
     * @var \SebastianBergmann\Diff\Differ
     */
    private $differ;
    /**
     * @readonly
     * @var \Symplify\PackageBuilder\Console\Formatter\ColorConsoleDiffFormatter
     */
    private $colorConsoleDiffFormatter;
    public function __construct(Differ $differ, ColorConsoleDiffFormatter $colorConsoleDiffFormatter)
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
