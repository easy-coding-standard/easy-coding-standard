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

class BundledComponentCollection implements \Countable, \IteratorAggregate
{
    /** @var BundledComponent[] */
    private $bundledComponents = [];
    public function add(\ECSPrefix20210803\PharIo\Manifest\BundledComponent $bundledComponent) : void
    {
        $this->bundledComponents[] = $bundledComponent;
    }
    /**
     * @return BundledComponent[]
     */
    public function getBundledComponents() : array
    {
        return $this->bundledComponents;
    }
    public function count() : int
    {
        return \count($this->bundledComponents);
    }
    public function getIterator() : \ECSPrefix20210803\PharIo\Manifest\BundledComponentCollectionIterator
    {
        return new \ECSPrefix20210803\PharIo\Manifest\BundledComponentCollectionIterator($this);
    }
}
