<?php

declare (strict_types=1);
/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace PhpCsFixer\Cache;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 */
final class NullCacheManager implements \PhpCsFixer\Cache\CacheManagerInterface
{
    /**
     * @param string $file
     * @param string $fileContent
     */
    public function needFixing($file, $fileContent) : bool
    {
        return \true;
    }
    /**
     * @param string $file
     * @param string $fileContent
     */
    public function setFile($file, $fileContent) : void
    {
    }
}
