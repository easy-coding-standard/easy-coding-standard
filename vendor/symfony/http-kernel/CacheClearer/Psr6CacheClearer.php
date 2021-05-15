<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210515\Symfony\Component\HttpKernel\CacheClearer;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class Psr6CacheClearer implements \ECSPrefix20210515\Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface
{
    private $pools = [];
    public function __construct(array $pools = [])
    {
        $this->pools = $pools;
    }
    /**
     * @param string $name
     */
    public function hasPool($name)
    {
        $name = (string) $name;
        return isset($this->pools[$name]);
    }
    /**
     * @param string $name
     */
    public function getPool($name)
    {
        $name = (string) $name;
        if (!$this->hasPool($name)) {
            throw new \InvalidArgumentException(\sprintf('Cache pool not found: "%s".', $name));
        }
        return $this->pools[$name];
    }
    /**
     * @param string $name
     */
    public function clearPool($name)
    {
        $name = (string) $name;
        if (!isset($this->pools[$name])) {
            throw new \InvalidArgumentException(\sprintf('Cache pool not found: "%s".', $name));
        }
        return $this->pools[$name]->clear();
    }
    /**
     * {@inheritdoc}
     * @param string $cacheDir
     */
    public function clear($cacheDir)
    {
        $cacheDir = (string) $cacheDir;
        foreach ($this->pools as $pool) {
            $pool->clear();
        }
    }
}
