<?php

declare (strict_types=1);
namespace ECSPrefix202208\Symplify\Skipper\Contract;

use ECSPrefix202208\Symplify\SmartFileSystem\SmartFileInfo;
interface SkipVoterInterface
{
    /**
     * @param string|object $element
     */
    public function match($element) : bool;
    /**
     * @param string|object $element
     * @param \Symplify\SmartFileSystem\SmartFileInfo|string $file
     */
    public function shouldSkip($element, $file) : bool;
}
