<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20211002\Symfony\Component\VarDumper\Caster;

use ECSPrefix20211002\Imagine\Image\ImageInterface;
use ECSPrefix20211002\Symfony\Component\VarDumper\Cloner\Stub;
/**
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
final class ImagineCaster
{
    public static function castImage(\ECSPrefix20211002\Imagine\Image\ImageInterface $c, array $a, \ECSPrefix20211002\Symfony\Component\VarDumper\Cloner\Stub $stub, bool $isNested) : array
    {
        $imgData = $c->get('png');
        if (\strlen($imgData) > 1 * 1000 * 1000) {
            $a += [\ECSPrefix20211002\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL . 'image' => new \ECSPrefix20211002\Symfony\Component\VarDumper\Caster\ConstStub($c->getSize())];
        } else {
            $a += [\ECSPrefix20211002\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL . 'image' => new \ECSPrefix20211002\Symfony\Component\VarDumper\Caster\ImgStub($imgData, 'image/png', $c->getSize())];
        }
        return $a;
    }
}
