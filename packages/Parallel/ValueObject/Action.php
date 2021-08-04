<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Parallel\ValueObject;

/**
 * @enum
 */
final class Action
{
    /**
     * @var string
     */
    public const QUIT = 'quit';
    /**
     * @var string
     */
    public const CHECK = 'check';
}
