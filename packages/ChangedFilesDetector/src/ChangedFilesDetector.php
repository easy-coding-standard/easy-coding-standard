<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ChangedFilesDetector;

use Nette\Caching\Cache;
use Symplify\EasyCodingStandard\ChangedFilesDetector\Cache\CacheFactory;
use Symplify\EasyCodingStandard\Configuration\ConfigFilePathHelper;

final class ChangedFilesDetector
{
    /**
     * @var string
     */
    private const CONFIGURATION_HASH_KEY = 'configuration_hash';

    /**
     * @var Cache
     */
    private $cache;

    public function __construct(CacheFactory $cacheFactory, ?string $configurationFile = null)
    {
        $this->cache = $cacheFactory->create();

        $configurationFile = $configurationFile ?: ConfigFilePathHelper::provide();
        if (file_exists($configurationFile)) {
            $this->storeConfigurationDataHash($this->hashFile($configurationFile));
        }
    }

    public function addFile(string $filePath): void
    {
        $hash = $this->hashFile($filePath);
        $this->cache->save($filePath, $hash);
    }

    public function invalidateFile(string $filePath): void
    {
        $this->cache->remove($filePath);
    }

    public function hasFileChanged(string $filePath): bool
    {
        $newFileHash = $this->hashFile($filePath);
        $oldFileHash = $this->cache->load($filePath);

        if ($newFileHash !== $oldFileHash) {
            $this->addFile($filePath);

            return true;
        }

        return false;
    }

    public function clearCache(): void
    {
        $this->cache->clean([Cache::ALL => true]);
    }

    private function hashFile(string $filePath): string
    {
        return md5_file($filePath);
    }

    private function storeConfigurationDataHash(string $configurationHash): void
    {
        $this->invalidateCacheIfConfigurationChanged($configurationHash);
        $this->cache->save(self::CONFIGURATION_HASH_KEY, $configurationHash);
    }

    private function invalidateCacheIfConfigurationChanged(string $configurationHash): void
    {
        $oldConfigurationHash = $this->cache->load(self::CONFIGURATION_HASH_KEY);
        if ($configurationHash !== $oldConfigurationHash) {
            $this->clearCache();
        }
    }
}
