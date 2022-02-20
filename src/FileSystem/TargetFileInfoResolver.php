<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\FileSystem;

use Symplify\EasyCodingStandard\SnippetFormatter\Provider\CurrentParentFileInfoProvider;
use ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo;
final class TargetFileInfoResolver
{
    /**
     * @var \Symplify\EasyCodingStandard\SnippetFormatter\Provider\CurrentParentFileInfoProvider
     */
    private $currentParentFileInfoProvider;
    public function __construct(\Symplify\EasyCodingStandard\SnippetFormatter\Provider\CurrentParentFileInfoProvider $currentParentFileInfoProvider)
    {
        $this->currentParentFileInfoProvider = $currentParentFileInfoProvider;
    }
    /**
     * Useful for @see \Symplify\EasyCodingStandard\SnippetFormatter\Command\CheckMarkdownCommand Where the
     * $smartFileInfo is only temporary snippet, so original markdown file should be used
     */
    public function resolveTargetFileInfo(\ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo) : \ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo
    {
        $currentParentFileInfo = $this->currentParentFileInfoProvider->provide();
        if ($currentParentFileInfo !== null) {
            return $currentParentFileInfo;
        }
        return $smartFileInfo;
    }
}
