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
interface CacheInterface
{
    public function getSignature() : \PhpCsFixer\Cache\SignatureInterface;
    /**
     * @param string $file
     */
    public function has($file) : bool;
    /**
     * @param string $file
     */
    public function get($file) : ?int;
    /**
     * @param string $file
     * @param int $hash
     */
    public function set($file, $hash) : void;
    /**
     * @param string $file
     */
    public function clear($file) : void;
    public function toJson() : string;
}
