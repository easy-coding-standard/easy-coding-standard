<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Contract\Finder;

use SplFileInfo;

interface ExtraFilesProviderInterface
{
    /**
     * @param string[] $source
     * @return SplFileInfo[]
     */
    public function provideForSource(array $source): array;
}
