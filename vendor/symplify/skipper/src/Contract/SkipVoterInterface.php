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
     * @return bool
     */
    public function shouldSkip($element, SmartFileInfo $smartFileInfo);
}
