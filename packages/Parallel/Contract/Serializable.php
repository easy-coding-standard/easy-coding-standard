<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Parallel\Contract;

use JsonSerializable;
interface Serializable extends \JsonSerializable
{
    /**
     * @param array<string, mixed> $json
     * @return $this
     */
    public static function decode($json);
}
