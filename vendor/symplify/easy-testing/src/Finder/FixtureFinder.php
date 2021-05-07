<?php

namespace Symplify\EasyTesting\Finder;

use ECSPrefix20210507\Symfony\Component\Finder\Finder;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\SmartFileInfo;
final class FixtureFinder
{
    /**
     * @var FinderSanitizer
     */
    private $finderSanitizer;
    /**
     * @param \Symplify\SmartFileSystem\Finder\FinderSanitizer $finderSanitizer
     */
    public function __construct($finderSanitizer)
    {
        $this->finderSanitizer = $finderSanitizer;
    }
    /**
     * @return mixed[]
     */
    public function find(array $sources)
    {
        $finder = new \ECSPrefix20210507\Symfony\Component\Finder\Finder();
        $finder->files()->in($sources)->name('*.php.inc')->path('Fixture')->sortByName();
        return $this->finderSanitizer->sanitize($finder);
    }
}
