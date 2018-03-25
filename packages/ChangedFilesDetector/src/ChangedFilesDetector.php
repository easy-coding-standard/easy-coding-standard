<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ChangedFilesDetector;

use Nette\Caching\Cache;
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
     * @var Cache
     */
    private $cache;

    /**
     * @var FileHashComputer
     */
    private $fileHashComputer;

    public function __construct(Cache $cache, FileHashComputer $fileHashComputer)
    {
        $this->cache = $cache;
        $this->fileHashComputer = $fileHashComputer;

        $configurationFile = ConfigFileFinder::provide('ecs');
        if ($configurationFile !== null && is_file($configurationFile)) {
            $this->storeConfigurationDataHash($this->fileHashComputer->compute($configurationFile));
        }
    }

    public function changeConfigurationFile(string $configurationFile): void
    {
        $this->storeConfigurationDataHash($this->fileHashComputer->compute($configurationFile));
    }

    public function addFile(string $filePath): void
    {
        $hash = $this->fileHashComputer->compute($filePath);
        $this->cache->save($filePath, $hash, [
            Cache::TAGS => self::CHANGED_FILES_CACHE_TAG,
        ]);
    }

    public function invalidateFile(string $filePath): void
    {
        $this->cache->remove($filePath);
    }

    public function hasFileChanged(string $filePath): bool
    {
        $newFileHash = $this->fileHashComputer->compute($filePath);
        $oldFileHash = $this->cache->load($filePath);

        if ($newFileHash !== $oldFileHash) {
            return true;
        }

        return false;
    }

    public function clearCache(): void
    {
        // clear cache only for changed files group
        $this->cache->clean([Cache::TAGS => self::CHANGED_FILES_CACHE_TAG]);
    }

    private function storeConfigurationDataHash(string $configurationHash): void
    {
        $this->invalidateCacheIfConfigurationChanged($configurationHash);
        $this->cache->save(self::CONFIGURATION_HASH_KEY, $configurationHash, [
            Cache::TAGS => self::CHANGED_FILES_CACHE_TAG,
        ]);
    }

    private function invalidateCacheIfConfigurationChanged(string $configurationHash): void
    {
        $oldConfigurationHash = $this->cache->load(self::CONFIGURATION_HASH_KEY);
        if ($configurationHash !== $oldConfigurationHash) {
            $this->clearCache();
        }
    }
}
