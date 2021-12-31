<?php

declare (strict_types=1);
namespace ECSPrefix20211231\Symplify\Skipper\Contract;

use ECSPrefix20211231\Symplify\SmartFileSystem\SmartFileInfo;
interface SkipVoterInterface
{
    /**
     * @param object|string $element
     */
    public function match($element) : bool;
    /**
     * @param object|string $element
     */
    public function shouldSkip($element, \ECSPrefix20211231\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo) : bool;
}
