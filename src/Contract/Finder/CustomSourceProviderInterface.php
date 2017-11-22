<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Contract\Finder;

use IteratorAggregate;

interface CustomSourceProviderInterface
{
    /**
     * @param string[] $source
     */
    public function find(array $source): IteratorAggregate;
}
