<?php

declare (strict_types=1);
namespace ECSPrefix20220607\Symplify\EasyCodingStandard\SnippetFormatter\Provider;

use ECSPrefix20220607\Symplify\SmartFileSystem\SmartFileInfo;
final class CurrentParentFileInfoProvider
{
    /**
     * @var \Symplify\SmartFileSystem\SmartFileInfo|null
     */
    private $smartFileInfo;
    public function setParentFileInfo(SmartFileInfo $smartFileInfo) : void
    {
        $this->smartFileInfo = $smartFileInfo;
    }
    public function provide() : ?SmartFileInfo
    {
        return $this->smartFileInfo;
    }
}
