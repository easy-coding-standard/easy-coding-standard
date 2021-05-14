<?php

namespace Symplify\EasyCodingStandard\ChangedFilesDetector;

use ECSPrefix20210514\Nette\Caching\Cache;
use Symplify\SmartFileSystem\SmartFileInfo;
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
    public function __construct(\Symplify\EasyCodingStandard\ChangedFilesDetector\FileHashComputer $fileHashComputer, \ECSPrefix20210514\Nette\Caching\Cache $cache)
    {
        $this->fileHashComputer = $fileHashComputer;
        $this->cache = $cache;
    }
    /**
     * For tests
     * @return void
     * @param string $configurationFile
     */
    public function changeConfigurationFile($configurationFile)
    {
        $configurationFile = (string) $configurationFile;
        $this->storeConfigurationDataHash($this->fileHashComputer->computeConfig($configurationFile));
    }
    /**
     * @return void
     */
    public function addFileInfo(\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
    {
        $cacheKey = $this->fileInfoToKey($smartFileInfo);
        $currentValue = $this->fileHashComputer->compute($smartFileInfo->getRealPath());
        $this->cache->save($cacheKey, $currentValue, [\ECSPrefix20210514\Nette\Caching\Cache::TAGS => [self::CHANGED_FILES_CACHE_TAG]]);
    }
    /**
     * @return void
     */
    public function invalidateFileInfo(\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
    {
        $cacheKey = $this->fileInfoToKey($smartFileInfo);
        $this->cache->remove($cacheKey);
    }
    /**
     * @return bool
     */
    public function hasFileInfoChanged(\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
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
        $this->cache->clean([\ECSPrefix20210514\Nette\Caching\Cache::TAGS => [self::CHANGED_FILES_CACHE_TAG]]);
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
     * @param string $configurationHash
     */
    private function storeConfigurationDataHash($configurationHash)
    {
        $configurationHash = (string) $configurationHash;
        $this->invalidateCacheIfConfigurationChanged($configurationHash);
        $this->cache->save(self::CONFIGURATION_HASH_KEY, $configurationHash);
    }
    /**
     * @return string
     */
    private function fileInfoToKey(\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
    {
        return \sha1($smartFileInfo->getRelativeFilePathFromCwd());
    }
    /**
     * @return void
     * @param string $configurationHash
     */
    private function invalidateCacheIfConfigurationChanged($configurationHash)
    {
        $configurationHash = (string) $configurationHash;
        $cachedValue = $this->cache->load(self::CONFIGURATION_HASH_KEY);
        if ($configurationHash === $cachedValue) {
            return;
        }
        $this->clearCache();
    }
}
