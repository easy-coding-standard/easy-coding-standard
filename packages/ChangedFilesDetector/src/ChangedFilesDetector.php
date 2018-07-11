<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ChangedFilesDetector;

use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\PackageBuilder\Configuration\ConfigFileFinder;

final class ChangedFilesDetector
{
    /**
     * @var string
     */
    public const CHANGED_FILES_CACHE_TAG = 'changed_files';

    /**
     * @var string
     */
    private const CONFIGURATION_HASH_KEY = 'configuration_hash';

    /**
     * @var FileHashComputer
     */
    private $fileHashComputer;

    /**
     * @var TagAwareAdapterInterface
     */
    private $tagAwareAdapter;

    public function __construct(FileHashComputer $fileHashComputer, TagAwareAdapterInterface $tagAwareAdapter)
    {
        $this->fileHashComputer = $fileHashComputer;
        $this->tagAwareAdapter = $tagAwareAdapter;

        $configurationFile = ConfigFileFinder::provide('ecs');
        if ($configurationFile !== null && is_file($configurationFile)) {
            $this->storeConfigurationDataHash($this->fileHashComputer->compute($configurationFile));
        }
    }

    public function changeConfigurationFile(string $configurationFile): void
    {
        $this->storeConfigurationDataHash($this->fileHashComputer->compute($configurationFile));
    }

    public function addFileInfo(SplFileInfo $fileInfo): void
    {
        $item = $this->tagAwareAdapter->getItem($this->fileInfoToKey($fileInfo));
        $item->set($this->fileHashComputer->compute($fileInfo->getRealPath()));
        $item->tag(self::CHANGED_FILES_CACHE_TAG);
        $this->tagAwareAdapter->save($item);
    }

    public function invalidateFileInfo(SplFileInfo $fileInfo): void
    {
        $this->tagAwareAdapter->deleteItem($this->fileInfoToKey($fileInfo));
    }

    public function hasFileInfoChanged(SplFileInfo $fileInfo): bool
    {
        $newFileHash = $this->fileHashComputer->compute($fileInfo->getRealPath());

        $cacheItem = $this->tagAwareAdapter->getItem($this->fileInfoToKey($fileInfo));
        $oldFileHash = $cacheItem->get();

        if ($newFileHash !== $oldFileHash) {
            return true;
        }

        return false;
    }

    public function clearCache(): void
    {
        // clear cache only for changed files group
        $this->tagAwareAdapter->invalidateTags([self::CHANGED_FILES_CACHE_TAG]);
    }

    private function storeConfigurationDataHash(string $configurationHash): void
    {
        $this->invalidateCacheIfConfigurationChanged($configurationHash);

        $cacheItem = $this->tagAwareAdapter->getItem(self::CONFIGURATION_HASH_KEY);
        $cacheItem->set($configurationHash);
        $this->tagAwareAdapter->save($cacheItem);
    }

    private function invalidateCacheIfConfigurationChanged(string $configurationHash): void
    {
        $cacheItem = $this->tagAwareAdapter->getItem(self::CONFIGURATION_HASH_KEY);

        $oldConfigurationHash = $cacheItem->get();
        if ($configurationHash !== $oldConfigurationHash) {
            $this->clearCache();
        }
    }

    private function fileInfoToKey(SplFileInfo $fileInfo): string
    {
        return sha1($fileInfo->getRealPath());
    }
}
