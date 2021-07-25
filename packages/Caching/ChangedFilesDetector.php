<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Caching;

use ECSPrefix20210725\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @see \Symplify\EasyCodingStandard\Tests\ChangedFilesDetector\ChangedFilesDetector\ChangedFilesDetectorTest
 */
final class ChangedFilesDetector
{
    /**
     * @var string
     */
    const CONFIGURATION_HASH_KEY = 'configuration_hash';
    /**
     * @var string
     */
    const FILE_HASH = 'file_hash';
    /**
     * @var \Symplify\EasyCodingStandard\Caching\FileHashComputer
     */
    private $fileHashComputer;
    /**
     * @var \Symplify\EasyCodingStandard\Caching\Cache
     */
    private $cache;
    public function __construct(\Symplify\EasyCodingStandard\Caching\FileHashComputer $fileHashComputer, \Symplify\EasyCodingStandard\Caching\Cache $cache)
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
    public function addFileInfo(\ECSPrefix20210725\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
    {
        $cacheKey = $this->fileInfoToKey($smartFileInfo);
        $currentValue = $this->fileHashComputer->compute($smartFileInfo->getRealPath());
        $this->cache->save($cacheKey, self::FILE_HASH, $currentValue);
    }
    /**
     * @return void
     */
    public function invalidateFileInfo(\ECSPrefix20210725\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
    {
        $cacheKey = $this->fileInfoToKey($smartFileInfo);
        $this->cache->clean($cacheKey);
    }
    public function hasFileInfoChanged(\ECSPrefix20210725\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo) : bool
    {
        $newFileHash = $this->fileHashComputer->compute($smartFileInfo->getRealPath());
        $cacheKey = $this->fileInfoToKey($smartFileInfo);
        $cachedValue = $this->cache->load($cacheKey, self::FILE_HASH);
        return $newFileHash !== $cachedValue;
    }
    /**
     * @return void
     */
    public function clearCache()
    {
        // clear cache only for changed files group
        $this->cache->clear();
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
        $this->cache->save(self::CONFIGURATION_HASH_KEY, self::FILE_HASH, $configurationHash);
    }
    private function fileInfoToKey(\ECSPrefix20210725\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo) : string
    {
        return \sha1($smartFileInfo->getRelativeFilePathFromCwd());
    }
    /**
     * @return void
     */
    private function invalidateCacheIfConfigurationChanged(string $configurationHash)
    {
        $cachedValue = $this->cache->load(self::CONFIGURATION_HASH_KEY, self::FILE_HASH);
        if ($configurationHash === $cachedValue) {
            return;
        }
        $this->clearCache();
    }
}
