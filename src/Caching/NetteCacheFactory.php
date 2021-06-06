<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Caching;

use ECSPrefix20210606\Nette\Caching\Cache;
use ECSPrefix20210606\Nette\Caching\Storages\FileStorage;
use Symplify\EasyCodingStandard\ValueObject\Option;
use ECSPrefix20210606\Symplify\PackageBuilder\Parameter\ParameterProvider;
use ECSPrefix20210606\Symplify\SmartFileSystem\SmartFileSystem;
final class NetteCacheFactory
{
    /**
     * @var ParameterProvider
     */
    private $parameterProvider;
    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;
    public function __construct(\ECSPrefix20210606\Symplify\PackageBuilder\Parameter\ParameterProvider $parameterProvider, \ECSPrefix20210606\Symplify\SmartFileSystem\SmartFileSystem $smartFileSystem)
    {
        $this->parameterProvider = $parameterProvider;
        $this->smartFileSystem = $smartFileSystem;
    }
    public function create() : \ECSPrefix20210606\Nette\Caching\Cache
    {
        $cacheDirectory = $this->parameterProvider->provideStringParameter(\Symplify\EasyCodingStandard\ValueObject\Option::CACHE_DIRECTORY);
        // ensure cache directory exists
        if (!$this->smartFileSystem->exists($cacheDirectory)) {
            $this->smartFileSystem->mkdir($cacheDirectory);
        }
        // journal is needed for tags support
        $journal = new \Symplify\EasyCodingStandard\Caching\JsonFileJournal($cacheDirectory . '/journal.json');
        $fileStorage = new \ECSPrefix20210606\Nette\Caching\Storages\FileStorage($cacheDirectory, $journal);
        // namespace is unique per project
        $namespace = \md5(\getcwd());
        return new \ECSPrefix20210606\Nette\Caching\Cache($fileStorage, $namespace);
    }
}
