<?php

declare (strict_types=1);
namespace ECSPrefix20211002\Symplify\Skipper\Contract;

use ECSPrefix20211002\Symplify\SmartFileSystem\SmartFileInfo;
interface SkipVoterInterface
{
    /**
     * @param string|object $element
     */
    public function match($element) : bool;
    /**
     * @param string|object $element
     * @param \Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo
     */
    public function shouldSkip($element, $smartFileInfo) : bool;
}
