<?php

declare (strict_types=1);
namespace ECSPrefix202208\Symplify\EasyParallel\Contract;

use JsonSerializable;
interface SerializableInterface extends JsonSerializable
{
    /**
     * @param array<string, mixed> $json
     */
    public static function decode(array $json) : self;
}
