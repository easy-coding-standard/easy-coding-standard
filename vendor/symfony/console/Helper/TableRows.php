<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix202408\Symfony\Component\Console\Helper;

/**
 * @internal
 */
class TableRows implements \IteratorAggregate
{
    /**
     * @var \Closure
     */
    private $generator;
    public function __construct(\Closure $generator)
    {
        $this->generator = $generator;
    }
    public function getIterator() : \Traversable
    {
        return ($this->generator)();
    }
}
