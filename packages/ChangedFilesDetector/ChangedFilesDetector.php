<?php

namespace Symplify\EasyCodingStandard\ChangedFilesDetector;

use ECSPrefix20210508\Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use ECSPrefix20210508\Symfony\Component\Cache\CacheItem;
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
     * @var TagAwareAdapterInterface
     */
    private $tagAwareAdapter;
    public function __construct(\Symplify\EasyCodingStandard\ChangedFilesDetector\FileHashComputer $fileHashComputer, \ECSPrefix20210508\Symfony\Component\Cache\Adapter\TagAwareAdapterInterface $tagAwareAdapter)
    {
        $this->fileHashComputer = $fileHashComputer;
        $this->tagAwareAdapter = $tagAwareAdapter;
    }
    /**
     * For tests
     * @return void
     * @param string $configurationFile
     */
    public function changeConfigurationFile($configurationFile)
    {
        if (\is_object($configurationFile)) {
            $configurationFile = (string) $configurationFile;
        }
        $this->storeConfigurationDataHash($this->fileHashComputer->computeConfig($configurationFile));
    }
    /**
     * @return void
     */
    public function addFileInfo(\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
    {
        /** @var CacheItem $cacheItem */
        $cacheItem = $this->tagAwareAdapter->getItem($this->fileInfoToKey($smartFileInfo));
        $cacheItem->set($this->fileHashComputer->compute($smartFileInfo->getRealPath()));
        $cacheItem->tag(self::CHANGED_FILES_CACHE_TAG);
        $this->tagAwareAdapter->save($cacheItem);
    }
    /**
     * @return void
     */
    public function invalidateFileInfo(\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
    {
        $this->tagAwareAdapter->deleteItem($this->fileInfoToKey($smartFileInfo));
    }
    /**
     * @return bool
     */
    public function hasFileInfoChanged(\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
    {
        $newFileHash = $this->fileHashComputer->compute($smartFileInfo->getRealPath());
        $cacheItem = $this->tagAwareAdapter->getItem($this->fileInfoToKey($smartFileInfo));
        $oldFileHash = $cacheItem->get();
        return $newFileHash !== $oldFileHash;
    }
    /**
     * @return void
     */
    public function clearCache()
    {
        // clear cache only for changed files group
        $this->tagAwareAdapter->invalidateTags([self::CHANGED_FILES_CACHE_TAG]);
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
        if (\is_object($configurationHash)) {
            $configurationHash = (string) $configurationHash;
        }
        $this->invalidateCacheIfConfigurationChanged($configurationHash);
        $cacheItem = $this->tagAwareAdapter->getItem(self::CONFIGURATION_HASH_KEY);
        $cacheItem->set($configurationHash);
        $this->tagAwareAdapter->save($cacheItem);
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
        if (\is_object($configurationHash)) {
            $configurationHash = (string) $configurationHash;
        }
        $cacheItem = $this->tagAwareAdapter->getItem(self::CONFIGURATION_HASH_KEY);
        $oldConfigurationHash = $cacheItem->get();
        if ($configurationHash !== $oldConfigurationHash) {
            $this->clearCache();
        }
    }
}
