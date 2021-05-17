<?php

declare (strict_types=1);
namespace ECSPrefix20210517\Symplify\SetConfigResolver\Contract;

use ECSPrefix20210517\Symplify\SetConfigResolver\ValueObject\Set;
interface SetProviderInterface
{
    /**
     * @return Set[]
     */
    public function provide() : array;
    /**
     * @return string[]
     */
    public function provideSetNames() : array;
    /**
     * @return \Symplify\SetConfigResolver\ValueObject\Set|null
     */
    public function provideByName(string $setName);
}
