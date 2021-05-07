<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Contracts\HttpClient;

/**
 * Yields response chunks, returned by HttpClientInterface::stream().
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
interface ResponseStreamInterface extends \Iterator
{
    /**
     * @return \ECSPrefix20210507\Symfony\Contracts\HttpClient\ResponseInterface
     */
    public function key();
    /**
     * @return \ECSPrefix20210507\Symfony\Contracts\HttpClient\ChunkInterface
     */
    public function current();
}
