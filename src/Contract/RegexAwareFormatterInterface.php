<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Contract;

interface RegexAwareFormatterInterface
{
    public function provideRegex(): string;
}
