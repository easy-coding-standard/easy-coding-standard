<?php

namespace ECSPrefix20210514\Symplify\SetConfigResolver\Contract;

use ECSPrefix20210514\Symplify\SetConfigResolver\ValueObject\Set;
interface SetProviderInterface
{
    /**
     * @return mixed[]
     */
    public function provide();
    /**
     * @return mixed[]
     */
    public function provideSetNames();
    /**
     * @return \Symplify\SetConfigResolver\ValueObject\Set|null
     * @param string $setName
     */
    public function provideByName($setName);
}
