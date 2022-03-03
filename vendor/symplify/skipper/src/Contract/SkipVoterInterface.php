<?php

declare (strict_types=1);
namespace ECSPrefix20220303\Symplify\Skipper\Contract;

use ECSPrefix20220303\Symplify\SmartFileSystem\SmartFileInfo;
interface SkipVoterInterface
{
    /**
     * @param object|string $element
     */
    public function match($element) : bool;
    /**
     * @param object|string $element
     */
    public function shouldSkip($element, \ECSPrefix20220303\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo) : bool;
}
