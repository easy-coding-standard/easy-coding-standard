<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Parallel\ValueObject;

final class ReactEvent
{
    /**
     * @var string
     */
    const EXIT = 'exit';
    /**
     * @var string
     */
    const DATA = 'data';
    /**
     * @var string
     */
    const ERROR = 'error';
}
