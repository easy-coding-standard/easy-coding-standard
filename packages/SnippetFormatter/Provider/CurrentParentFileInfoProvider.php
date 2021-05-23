<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\SnippetFormatter\Provider;

use ECSPrefix20210523\Symplify\SmartFileSystem\SmartFileInfo;
final class CurrentParentFileInfoProvider
{
    /**
     * @var SmartFileInfo|null
     */
    private $smartFileInfo;
    /**
     * @return void
     */
    public function setParentFileInfo(\ECSPrefix20210523\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
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
