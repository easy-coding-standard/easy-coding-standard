<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Component\VarDumper\Caster;

use ECSPrefix20210507\Imagine\Image\ImageInterface;
use ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub;
/**
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
final class ImagineCaster
{
    /**
     * @param \ECSPrefix20210507\Imagine\Image\ImageInterface $c
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     * @return mixed[]
     */
    public static function castImage($c, array $a, $stub, $isNested)
    {
        $imgData = $c->get('png');
        if (\strlen($imgData) > 1 * 1000 * 1000) {
            $a += [\ECSPrefix20210507\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL . 'image' => new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\ConstStub($c->getSize())];
        } else {
            $a += [\ECSPrefix20210507\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL . 'image' => new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\ImgStub($imgData, 'image/png', $c->getSize())];
        }
        return $a;
    }
}
