<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FileSystem;

use Symplify\EasyCodingStandard\SnippetFormatter\Provider\CurrentParentFileInfoProvider;
use Symplify\SmartFileSystem\SmartFileInfo;

final class TargetFileInfoResolver
{
    public function __construct(
        private CurrentParentFileInfoProvider $currentParentFileInfoProvider
    ) {
    }

    /**
     * Useful for @see \Symplify\EasyCodingStandard\SnippetFormatter\Command\CheckMarkdownCommand Where the
     * $smartFileInfo is only temporary snippet, so original markdown file should be used
     */
    public function resolveTargetFileInfo(SmartFileInfo $smartFileInfo): SmartFileInfo
    {
        $currentParentFileInfo = $this->currentParentFileInfoProvider->provide();
        if ($currentParentFileInfo !== null) {
            return $currentParentFileInfo;
        }

        return $smartFileInfo;
    }
}
