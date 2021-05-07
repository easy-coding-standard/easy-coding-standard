<?php

namespace Symplify\EasyCodingStandard\FileSystem;

use Symplify\EasyCodingStandard\SnippetFormatter\Provider\CurrentParentFileInfoProvider;
use Symplify\SmartFileSystem\SmartFileInfo;
final class TargetFileInfoResolver
{
    /**
     * @var CurrentParentFileInfoProvider
     */
    private $currentParentFileInfoProvider;
    /**
     * @param \Symplify\EasyCodingStandard\SnippetFormatter\Provider\CurrentParentFileInfoProvider $currentParentFileInfoProvider
     */
    public function __construct($currentParentFileInfoProvider)
    {
        $this->currentParentFileInfoProvider = $currentParentFileInfoProvider;
    }
    /**
     * Useful for @see \Symplify\EasyCodingStandard\SnippetFormatter\Command\CheckMarkdownCommand Where the
     * $smartFileInfo is only temporary snippet, so original markdown file should be used
     * @param \Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo
     * @return \Symplify\SmartFileSystem\SmartFileInfo
     */
    public function resolveTargetFileInfo($smartFileInfo)
    {
        $currentParentFileInfo = $this->currentParentFileInfoProvider->provide();
        if ($currentParentFileInfo !== null) {
            return $currentParentFileInfo;
        }
        return $smartFileInfo;
    }
}
