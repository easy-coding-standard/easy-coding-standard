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

class BundlesElement extends \ECSPrefix20210803\PharIo\Manifest\ManifestElement
{
    public function getComponentElements() : \ECSPrefix20210803\PharIo\Manifest\ComponentElementCollection
    {
        return new \ECSPrefix20210803\PharIo\Manifest\ComponentElementCollection($this->getChildrenByName('component'));
    }
}
