<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Contract\Finder;

use IteratorAggregate;
use Nette\Utils\Finder as NetteFinder;
use Symfony\Component\Finder\Finder as SymfonyFinder;

interface CustomSourceProviderInterface
{
    /**
     * @param string[] $source
     * @return NetteFinder|SymfonyFinder
     */
    public function find(array $source): IteratorAggregate;
}
