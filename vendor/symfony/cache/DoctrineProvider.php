<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\Symfony\Component\Cache;

use ECSPrefix20210508\Doctrine\Common\Cache\CacheProvider;
use ECSPrefix20210508\Psr\Cache\CacheItemPoolInterface;
use ECSPrefix20210508\Symfony\Contracts\Service\ResetInterface;
/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class DoctrineProvider extends \ECSPrefix20210508\Doctrine\Common\Cache\CacheProvider implements \ECSPrefix20210508\Symfony\Component\Cache\PruneableInterface, \ECSPrefix20210508\Symfony\Component\Cache\ResettableInterface
{
    private $pool;
    public function __construct(\ECSPrefix20210508\Psr\Cache\CacheItemPoolInterface $pool)
    {
        $this->pool = $pool;
    }
    /**
     * {@inheritdoc}
     */
    public function prune()
    {
        return $this->pool instanceof \ECSPrefix20210508\Symfony\Component\Cache\PruneableInterface && $this->pool->prune();
    }
    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        if ($this->pool instanceof \ECSPrefix20210508\Symfony\Contracts\Service\ResetInterface) {
            $this->pool->reset();
        }
        $this->setNamespace($this->getNamespace());
    }
    /**
     * {@inheritdoc}
     */
    protected function doFetch($id)
    {
        if (\is_object($id)) {
            $id = (string) $id;
        }
        $item = $this->pool->getItem(\rawurlencode($id));
        return $item->isHit() ? $item->get() : \false;
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    protected function doContains($id)
    {
        if (\is_object($id)) {
            $id = (string) $id;
        }
        return $this->pool->hasItem(\rawurlencode($id));
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        if (\is_object($data)) {
            $data = (string) $data;
        }
        if (\is_object($id)) {
            $id = (string) $id;
        }
        $item = $this->pool->getItem(\rawurlencode($id));
        if (0 < $lifeTime) {
            $item->expiresAfter($lifeTime);
        }
        return $this->pool->save($item->set($data));
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    protected function doDelete($id)
    {
        if (\is_object($id)) {
            $id = (string) $id;
        }
        return $this->pool->deleteItem(\rawurlencode($id));
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    protected function doFlush()
    {
        return $this->pool->clear();
    }
    /**
     * {@inheritdoc}
     *
     * @return array|null
     */
    protected function doGetStats()
    {
        return null;
    }
}
