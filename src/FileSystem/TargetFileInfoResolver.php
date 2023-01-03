<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FileSystem;

use SplFileInfo;
use Symplify\EasyCodingStandard\SnippetFormatter\Provider\CurrentParentFileInfoProvider;

final class TargetFileInfoResolver
{
    public function __construct(
        private readonly CurrentParentFileInfoProvider $currentParentFileInfoProvider
    ) {
    }

    /**
     * Useful for @see \Symplify\EasyCodingStandard\SnippetFormatter\Command\CheckMarkdownCommand Where the
     * $smartFileInfo is only temporary snippet, so original markdown file should be used
     */
    public function resolveTargetFileInfo(SplFileInfo $fileInfo): SplFileInfo
    {
        $currentParentFileInfo = $this->currentParentFileInfoProvider->provide();
        if ($currentParentFileInfo instanceof SplFileInfo) {
            return $currentParentFileInfo;
        }

        return $fileInfo;
    }
}
