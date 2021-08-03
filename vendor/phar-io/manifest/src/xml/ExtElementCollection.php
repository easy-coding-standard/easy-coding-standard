<?php

declare (strict_types=1);
/*
 * This file is part of PharIo\Manifest.
 *
 * (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210803\PharIo\Manifest;

class ExtElementCollection extends \ECSPrefix20210803\PharIo\Manifest\ElementCollection
{
    public function current() : \ECSPrefix20210803\PharIo\Manifest\ExtElement
    {
        return new \ECSPrefix20210803\PharIo\Manifest\ExtElement($this->getCurrentElement());
    }
}
