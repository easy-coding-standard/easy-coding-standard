<?php

namespace ECSPrefix20210516\Symplify\EasyTesting\Finder;

use ECSPrefix20210516\Symfony\Component\Finder\Finder;
use ECSPrefix20210516\Symplify\SmartFileSystem\Finder\FinderSanitizer;
use ECSPrefix20210516\Symplify\SmartFileSystem\SmartFileInfo;
final class FixtureFinder
{
    /**
     * @var FinderSanitizer
     */
    private $finderSanitizer;
    public function __construct(\ECSPrefix20210516\Symplify\SmartFileSystem\Finder\FinderSanitizer $finderSanitizer)
    {
        $this->finderSanitizer = $finderSanitizer;
    }
    /**
     * @return mixed[]
     */
    public function find(array $sources)
    {
        $finder = new \ECSPrefix20210516\Symfony\Component\Finder\Finder();
        $finder->files()->in($sources)->name('*.php.inc')->path('Fixture')->sortByName();
        return $this->finderSanitizer->sanitize($finder);
    }
}
