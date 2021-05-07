<?php

namespace Symplify\Skipper\Contract;

use Symplify\SmartFileSystem\SmartFileInfo;
interface SkipVoterInterface
{
    /**
     * @param string|object $element
     * @return bool
     */
    public function match($element);
    /**
     * @param string|object $element
     * @param \Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo
     * @return bool
     */
    public function shouldSkip($element, $smartFileInfo);
}
