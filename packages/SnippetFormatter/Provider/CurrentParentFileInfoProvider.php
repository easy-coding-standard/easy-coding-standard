<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\SnippetFormatter\Provider;

use ECSPrefix20210715\Symplify\SmartFileSystem\SmartFileInfo;
final class CurrentParentFileInfoProvider
{
    /**
     * @var \Symplify\SmartFileSystem\SmartFileInfo|null
     */
    private $smartFileInfo;
    /**
     * @return void
     */
    public function setParentFileInfo(\ECSPrefix20210715\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
    {
        $this->smartFileInfo = $smartFileInfo;
    }
    /**
     * @return \Symplify\SmartFileSystem\SmartFileInfo|null
     */
    public function provide()
    {
        return $this->smartFileInfo;
    }
}
