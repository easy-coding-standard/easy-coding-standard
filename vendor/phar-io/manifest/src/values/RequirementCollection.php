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

class RequirementCollection implements \Countable, \IteratorAggregate
{
    /** @var Requirement[] */
    private $requirements = [];
    public function add(\ECSPrefix20210803\PharIo\Manifest\Requirement $requirement) : void
    {
        $this->requirements[] = $requirement;
    }
    /**
     * @return Requirement[]
     */
    public function getRequirements() : array
    {
        return $this->requirements;
    }
    public function count() : int
    {
        return \count($this->requirements);
    }
    public function getIterator() : \ECSPrefix20210803\PharIo\Manifest\RequirementCollectionIterator
    {
        return new \ECSPrefix20210803\PharIo\Manifest\RequirementCollectionIterator($this);
    }
}
