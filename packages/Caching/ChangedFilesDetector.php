<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Caching;

use ECSPrefix202206\Symplify\SmartFileSystem\SmartFileInfo;
use ECSPrefix202206\Webmozart\Assert\Assert;
/**
 * @see \Symplify\EasyCodingStandard\Tests\ChangedFilesDetector\ChangedFilesDetector\ChangedFilesDetectorTest
 */
final class ChangedFilesDetector
{
    /**
     * @var string
     */
    private const CONFIGURATION_HASH_KEY = 'configuration_hash';
    /**
     * @var string
     */
    private const FILE_HASH = 'file_hash';
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
     */
    public function changeConfigurationFile(string $configurationFile) : void
    {
        $this->storeConfigurationDataHash($this->fileHashComputer->computeConfig($configurationFile));
    }
    public function addFileInfo(SmartFileInfo $smartFileInfo) : void
    {
        $cacheKey = $this->fileInfoToKey($smartFileInfo);
        $currentValue = $this->fileHashComputer->compute($smartFileInfo->getRealPath());
        $this->cache->save($cacheKey, self::FILE_HASH, $currentValue);
    }
    public function invalidateFileInfo(SmartFileInfo $smartFileInfo) : void
    {
        $cacheKey = $this->fileInfoToKey($smartFileInfo);
        $this->cache->clean($cacheKey);
    }
    public function hasFileInfoChanged(SmartFileInfo $smartFileInfo) : bool
    {
        $newFileHash = $this->fileHashComputer->compute($smartFileInfo->getRealPath());
        $cacheKey = $this->fileInfoToKey($smartFileInfo);
        $cachedValue = $this->cache->load($cacheKey, self::FILE_HASH);
        return $newFileHash !== $cachedValue;
    }
    public function clearCache() : void
    {
        // clear cache only for changed files group
        $this->cache->clear();
    }
    /**
     * For cache invalidation
     *
     * @param string[] $configFiles
     * @api
     */
    public function setUsedConfigs(array $configFiles) : void
    {
        if ($configFiles === []) {
            return;
        }
        Assert::allString($configFiles);
        Assert::allFile($configFiles);
        // the first config is core to all → if it was changed, just invalidate it
        $firstConfigFile = $configFiles[0];
        $this->storeConfigurationDataHash($this->fileHashComputer->computeConfig($firstConfigFile));
    }
    private function storeConfigurationDataHash(string $configurationHash) : void
    {
        $this->invalidateCacheIfConfigurationChanged($configurationHash);
        $this->cache->save(self::CONFIGURATION_HASH_KEY, self::FILE_HASH, $configurationHash);
    }
    private function fileInfoToKey(SmartFileInfo $smartFileInfo) : string
    {
        return \sha1($smartFileInfo->getRelativeFilePathFromCwd());
    }
    private function invalidateCacheIfConfigurationChanged(string $configurationHash) : void
    {
        $cachedValue = $this->cache->load(self::CONFIGURATION_HASH_KEY, self::FILE_HASH);
        if ($cachedValue === null) {
            return;
        }
        if ($configurationHash === $cachedValue) {
            return;
        }
        $this->clearCache();
    }
}
