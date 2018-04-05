<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ChangedFilesDetector;

use Psr\SimpleCache\CacheInterface;
use Symplify\PackageBuilder\Configuration\ConfigFileFinder;
use Symplify\PackageBuilder\FileSystem\FileGuard;

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
     * @var FileGuard
     */
    private $fileGuard;

    /**
     * @var CacheInterface
     */
    private $cache;

    public function __construct(
        FileHashComputer $fileHashComputer,
        FileGuard $fileGuard,
        CacheInterface $cache
    ) {
        $this->fileHashComputer = $fileHashComputer;
        $this->fileGuard = $fileGuard;
        $this->cache = $cache;

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
        $this->fileGuard->ensureIsAbsolutePath($filePath, __METHOD__);

        $hash = $this->fileHashComputer->compute($filePath);
        $this->cache->set($this->filePathToKey($filePath), $hash);
    }

    public function invalidateFile(string $filePath): void
    {
        $this->fileGuard->ensureIsAbsolutePath($filePath, __METHOD__);

        $this->cache->delete($this->filePathToKey($filePath));
    }

    public function hasFileChanged(string $filePath): bool
    {
        $this->fileGuard->ensureIsAbsolutePath($filePath, __METHOD__);

        $newFileHash = $this->fileHashComputer->compute($filePath);
        $oldFileHash = $this->cache->get($this->filePathToKey($filePath));

        if ($newFileHash !== $oldFileHash) {
            return true;
        }

        return false;
    }

    public function clearCache(): void
    {
        // clear cache only for changed files group
        $this->cache->clear();
    }

    private function storeConfigurationDataHash(string $configurationHash): void
    {
        $this->invalidateCacheIfConfigurationChanged($configurationHash);

        $this->cache->set(self::CONFIGURATION_HASH_KEY, $configurationHash);
    }

    private function invalidateCacheIfConfigurationChanged(string $configurationHash): void
    {
        $oldConfigurationHash = $this->cache->get(self::CONFIGURATION_HASH_KEY);
        if ($configurationHash !== $oldConfigurationHash) {
            $this->clearCache();
        }
    }

    private function filePathToKey(string $filePath): string
    {
        return sha1($filePath);
    }
}
