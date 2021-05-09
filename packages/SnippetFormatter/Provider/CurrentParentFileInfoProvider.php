<?php

namespace Symplify\EasyCodingStandard\SnippetFormatter\Provider;

use Symplify\SmartFileSystem\SmartFileInfo;

final class CurrentParentFileInfoProvider
{
    /**
     * @var SmartFileInfo|null
     */
    private $smartFileInfo;

    /**
     * @return void
     */
    public function setParentFileInfo(SmartFileInfo $smartFileInfo)
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
