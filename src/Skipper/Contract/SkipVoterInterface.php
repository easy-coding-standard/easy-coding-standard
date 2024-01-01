<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Skipper\Contract;

use SplFileInfo;

interface SkipVoterInterface
{
    public function match(string | object $element): bool;

    public function shouldSkip(string | object $element, SplFileInfo | string $file): bool;
}
