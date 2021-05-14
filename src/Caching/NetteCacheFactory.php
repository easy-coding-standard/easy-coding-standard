<?php

namespace Symplify\EasyCodingStandard\Caching;

use ECSPrefix20210514\Nette\Caching\Cache;
use ECSPrefix20210514\Nette\Caching\Storages\FileStorage;
use ECSPrefix20210514\Nette\Caching\Storages\SQLiteJournal;
use Symplify\EasyCodingStandard\ValueObject\Option;
use ECSPrefix20210514\Symplify\PackageBuilder\Parameter\ParameterProvider;
use ECSPrefix20210514\Symplify\SmartFileSystem\SmartFileSystem;
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
    public function __construct(\ECSPrefix20210514\Symplify\PackageBuilder\Parameter\ParameterProvider $parameterProvider, \ECSPrefix20210514\Symplify\SmartFileSystem\SmartFileSystem $smartFileSystem)
    {
        $this->parameterProvider = $parameterProvider;
        $this->smartFileSystem = $smartFileSystem;
    }
    /**
     * @return \Nette\Caching\Cache
     */
    public function create()
    {
        $cacheDirectory = $this->parameterProvider->provideStringParameter(\Symplify\EasyCodingStandard\ValueObject\Option::CACHE_DIRECTORY);
        // ensure cache directory exists
        if (!$this->smartFileSystem->exists($cacheDirectory)) {
            $this->smartFileSystem->mkdir($cacheDirectory);
        }
        // journal is needed for tags support
        $sqlLiteJournal = new \ECSPrefix20210514\Nette\Caching\Storages\SQLiteJournal($cacheDirectory . '/_tags_journal');
        $fileStorage = new \ECSPrefix20210514\Nette\Caching\Storages\FileStorage($cacheDirectory, $sqlLiteJournal);
        // namespace is unique per project
        $namespace = \md5(\getcwd());
        return new \ECSPrefix20210514\Nette\Caching\Cache($fileStorage, $namespace);
    }
}
