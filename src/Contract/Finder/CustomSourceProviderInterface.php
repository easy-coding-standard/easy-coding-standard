<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Contract\Finder;

use Nette\Utils\Finder as NetteFinder;
use SplFileInfo;
use Symfony\Component\Finder\Finder as SymfonyFinder;

interface CustomSourceProviderInterface
{
    /**
     * @param string[] $source
     *
     * @return NetteFinder|SymfonyFinder|SplFileInfo[]|string[]|iterable Removed from typehint for BC
     */
    public function find(array $source);
}
