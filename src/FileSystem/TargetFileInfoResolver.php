<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FileSystem;

use Symplify\EasyCodingStandard\SnippetFormatter\Provider\CurrentParentFilePathProvider;

final class TargetFileInfoResolver
{
    public function __construct(
        private readonly CurrentParentFilePathProvider $currentParentFilePathProvider
    ) {
    }

    /**
     * Useful for @see \Symplify\EasyCodingStandard\SnippetFormatter\Command\CheckMarkdownCommand Where the
     * $smartFileInfo is only temporary snippet, so original markdown file should be used
     */
    public function resolveTargetFilePath(string $filePath): string
    {
        $currentParentFilePath = $this->currentParentFilePathProvider->provide();
        if (is_string($currentParentFilePath)) {
            return $currentParentFilePath;
        }

        return $filePath;
    }
}
