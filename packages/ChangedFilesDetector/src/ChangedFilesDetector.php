<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ChangedFilesDetector;

use Nette\Caching\Cache;
use Symplify\PackageBuilder\Configuration\ConfigFilePathHelper;

final class ChangedFilesDetector
{
    /**
     * @var string
     */
    private const CONFIGURATION_HASH_KEY = 'configuration_hash';

    /**
     * @var Cache|null
     */
    private $cache;

    /**
     * @var FileHashComputer
     */
    private $fileHashComputer;

    public function __construct(FileHashComputer $fileHashComputer)
    {
        $this->fileHashComputer = $fileHashComputer;
    }

    public function setCache(?Cache $cache): void
    {
        $this->cache = $cache;
        if ($this->cache === null) {
            return;
        }

        $configurationFile = ConfigFilePathHelper::provide('ecs');
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
        if ($this->cache === null) {
            return;
        }

        $hash = $this->fileHashComputer->compute($filePath);
        $this->cache->save($filePath, $hash);
    }

    public function invalidateFile(string $filePath): void
    {
        if ($this->cache !== null) {
            $this->cache->remove($filePath);
        }
    }

    public function hasFileChanged(string $filePath): bool
    {
        if ($this->cache === null) {
            return true;
        }

        $newFileHash = $this->fileHashComputer->compute($filePath);
        $oldFileHash = $this->cache->load($filePath);

        if ($newFileHash !== $oldFileHash) {
            return true;
        }

        return false;
    }

    public function clearCache(): void
    {
        if ($this->cache !== null) {
            $this->cache->clean([Cache::ALL => true]);
        }
    }

    private function storeConfigurationDataHash(string $configurationHash): void
    {
        if ($this->cache === null) {
            return;
        }

        $this->invalidateCacheIfConfigurationChanged($configurationHash);
        $this->cache->save(self::CONFIGURATION_HASH_KEY, $configurationHash);
    }

    private function invalidateCacheIfConfigurationChanged(string $configurationHash): void
    {
        if ($this->cache === null) {
            return;
        }

        $oldConfigurationHash = $this->cache->load(self::CONFIGURATION_HASH_KEY);
        if ($configurationHash !== $oldConfigurationHash) {
            $this->clearCache();
        }
    }
}
