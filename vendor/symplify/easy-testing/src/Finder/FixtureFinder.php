<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\EasyTesting\Finder;

use ConfigTransformer20210601\Symfony\Component\Finder\Finder;
use ConfigTransformer20210601\Symplify\SmartFileSystem\Finder\FinderSanitizer;
use ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileInfo;
final class FixtureFinder
{
    /**
     * @var FinderSanitizer
     */
    private $finderSanitizer;
    public function __construct(\ConfigTransformer20210601\Symplify\SmartFileSystem\Finder\FinderSanitizer $finderSanitizer)
    {
        $this->finderSanitizer = $finderSanitizer;
    }
    /**
     * @return SmartFileInfo[]
     */
    public function find(array $sources) : array
    {
        $finder = new \ConfigTransformer20210601\Symfony\Component\Finder\Finder();
        $finder->files()->in($sources)->name('*.php.inc')->path('Fixture')->sortByName();
        return $this->finderSanitizer->sanitize($finder);
    }
}
