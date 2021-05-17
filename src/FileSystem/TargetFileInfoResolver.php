<?php

namespace Symplify\EasyCodingStandard\FileSystem;

use Symplify\EasyCodingStandard\SnippetFormatter\Provider\CurrentParentFileInfoProvider;
use ECSPrefix20210517\Symplify\SmartFileSystem\SmartFileInfo;
final class TargetFileInfoResolver
{
    /**
     * @var CurrentParentFileInfoProvider
     */
    private $currentParentFileInfoProvider;
    public function __construct(\Symplify\EasyCodingStandard\SnippetFormatter\Provider\CurrentParentFileInfoProvider $currentParentFileInfoProvider)
    {
        $this->currentParentFileInfoProvider = $currentParentFileInfoProvider;
    }
    /**
     * Useful for @see \Symplify\EasyCodingStandard\SnippetFormatter\Command\CheckMarkdownCommand Where the
     * $smartFileInfo is only temporary snippet, so original markdown file should be used
     * @return \Symplify\SmartFileSystem\SmartFileInfo
     */
    public function resolveTargetFileInfo(\ECSPrefix20210517\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
    {
        $currentParentFileInfo = $this->currentParentFileInfoProvider->provide();
        if ($currentParentFileInfo !== null) {
            return $currentParentFileInfo;
        }
        return $smartFileInfo;
    }
}
