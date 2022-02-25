<?php

declare (strict_types=1);
namespace ECSPrefix20220225\Symplify\Skipper\Contract;

use ECSPrefix20220225\Symplify\SmartFileSystem\SmartFileInfo;
interface SkipVoterInterface
{
    /**
     * @param object|string $element
     */
    public function match($element) : bool;
    /**
     * @param object|string $element
     */
    public function shouldSkip($element, \ECSPrefix20220225\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo) : bool;
}
