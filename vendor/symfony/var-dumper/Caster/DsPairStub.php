<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix202306\Symfony\Component\VarDumper\Caster;

use ECSPrefix202306\Symfony\Component\VarDumper\Cloner\Stub;
/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class DsPairStub extends Stub
{
    /**
     * @param string|int $key
     * @param mixed $value
     */
    public function __construct($key, $value)
    {
        $this->value = [Caster::PREFIX_VIRTUAL . 'key' => $key, Caster::PREFIX_VIRTUAL . 'value' => $value];
    }
}
