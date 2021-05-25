<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\ChangedFilesDetector;

use ECSPrefix20210525\Nette\Caching\Cache;
use ECSPrefix20210525\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @see \Symplify\EasyCodingStandard\Tests\ChangedFilesDetector\ChangedFilesDetector\ChangedFilesDetectorTest
 */
final class ChangedFilesDetector
{
    /**
     * @var string
     */
    const CHANGED_FILES_CACHE_TAG = 'changed_files';
    /**
     * @var string
     */
    const CONFIGURATION_HASH_KEY = 'configuration_hash';
    /**
     * @var FileHashComputer
     */
    private $fileHashComputer;
    /**
     * @var Cache
     */
    private $cache;
    public function __construct(\Symplify\EasyCodingStandard\ChangedFilesDetector\FileHashComputer $fileHashComputer, \ECSPrefix20210525\Nette\Caching\Cache $cache)
    {
        $this->fileHashComputer = $fileHashComputer;
        $this->cache = $cache;
    }
    /**
     * For tests
     * @return void
     */
    public function changeConfigurationFile(string $configurationFile)
    {
        $this->storeConfigurationDataHash($this->fileHashComputer->computeConfig($configurationFile));
    }
    /**
     * @return void
     */
    public function addFileInfo(\ECSPrefix20210525\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
    {
        $cacheKey = $this->fileInfoToKey($smartFileInfo);
        $currentValue = $this->fileHashComputer->compute($smartFileInfo->getRealPath());
        $this->cache->save($cacheKey, $currentValue, [\ECSPrefix20210525\Nette\Caching\Cache::TAGS => [self::CHANGED_FILES_CACHE_TAG]]);
    }
    /**
     * @return void
     */
    public function invalidateFileInfo(\ECSPrefix20210525\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
    {
        $cacheKey = $this->fileInfoToKey($smartFileInfo);
        $this->cache->remove($cacheKey);
    }
    public function hasFileInfoChanged(\ECSPrefix20210525\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo) : bool
    {
        $newFileHash = $this->fileHashComputer->compute($smartFileInfo->getRealPath());
        $cacheKey = $this->fileInfoToKey($smartFileInfo);
        $cachedValue = $this->cache->load($cacheKey);
        return $newFileHash !== $cachedValue;
    }
    /**
     * @return void
     */
    public function clearCache()
    {
        // clear cache only for changed files group
        $this->cache->clean([\ECSPrefix20210525\Nette\Caching\Cache::TAGS => [self::CHANGED_FILES_CACHE_TAG]]);
    }
    /**
     * For cache invalidation
     *
     * @api
     * @param SmartFileInfo[] $configFileInfos
     * @return void
     */
    public function setUsedConfigs(array $configFileInfos)
    {
        if ($configFileInfos === []) {
            return;
        }
        // the first config is core to all → if it was changed, just invalidate it
        $firstConfigFileInfo = $configFileInfos[0];
        $this->storeConfigurationDataHash($this->fileHashComputer->computeConfig($firstConfigFileInfo->getRealPath()));
    }
    /**
     * @return void
     */
    private function storeConfigurationDataHash(string $configurationHash)
    {
        $this->invalidateCacheIfConfigurationChanged($configurationHash);
        $this->cache->save(self::CONFIGURATION_HASH_KEY, $configurationHash);
    }
    private function fileInfoToKey(\ECSPrefix20210525\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo) : string
    {
        return \sha1($smartFileInfo->getRelativeFilePathFromCwd());
    }
    /**
     * @return void
     */
    private function invalidateCacheIfConfigurationChanged(string $configurationHash)
    {
        $cachedValue = $this->cache->load(self::CONFIGURATION_HASH_KEY);
        if ($configurationHash === $cachedValue) {
            return;
        }
        $this->clearCache();
    }
}
