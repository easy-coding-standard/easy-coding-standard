<?php

namespace ECSPrefix20210516\Symplify\Skipper\Contract;

use ECSPrefix20210516\Symplify\SmartFileSystem\SmartFileInfo;
interface SkipVoterInterface
{
    /**
     * @param string|object $element
     * @return bool
     */
    public function match($element);
    /**
     * @param string|object $element
     * @return bool
     */
    public function shouldSkip($element, \ECSPrefix20210516\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo);
}
