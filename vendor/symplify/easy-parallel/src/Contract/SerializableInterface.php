<?php

declare (strict_types=1);
namespace ECSPrefix202412\Symplify\EasyParallel\Contract;

use JsonSerializable;
/**
 * @api
 */
interface SerializableInterface extends JsonSerializable
{
    /**
     * @param array<string, mixed> $json
     */
    public static function decode(array $json) : self;
}
