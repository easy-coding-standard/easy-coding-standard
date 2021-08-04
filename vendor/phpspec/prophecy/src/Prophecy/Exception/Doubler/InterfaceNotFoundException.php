<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210804\Prophecy\Exception\Doubler;

class InterfaceNotFoundException extends \ECSPrefix20210804\Prophecy\Exception\Doubler\ClassNotFoundException
{
    public function getInterfaceName()
    {
        return $this->getClassname();
    }
}
