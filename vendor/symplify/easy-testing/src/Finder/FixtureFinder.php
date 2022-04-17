<?php

declare (strict_types=1);
namespace ECSPrefix20220417\Symplify\EasyTesting\Finder;

use ECSPrefix20220417\Symfony\Component\Finder\Finder;
use ECSPrefix20220417\Symplify\SmartFileSystem\Finder\FinderSanitizer;
use ECSPrefix20220417\Symplify\SmartFileSystem\SmartFileInfo;
final class FixtureFinder
{
    /**
     * @var \Symplify\SmartFileSystem\Finder\FinderSanitizer
     */
    private $finderSanitizer;
    public function __construct(\ECSPrefix20220417\Symplify\SmartFileSystem\Finder\FinderSanitizer $finderSanitizer)
    {
        $this->finderSanitizer = $finderSanitizer;
    }
    /**
     * @param string[] $sources
     * @return SmartFileInfo[]
     */
    public function find(array $sources) : array
    {
        $finder = new \ECSPrefix20220417\Symfony\Component\Finder\Finder();
        $finder->files()->in($sources)->name('*.php.inc')->path('Fixture')->sortByName();
        return $this->finderSanitizer->sanitize($finder);
    }
}
