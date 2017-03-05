<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ChangedFilesDetector;

use Nette\Caching\Cache;
use Symplify\EasyCodingStandard\ChangedFilesDetector\Cache\CacheFactory;
use Symplify\EasyCodingStandard\ChangedFilesDetector\Contract\ChangedFilesDetectorInterface;
use Symplify\EasyCodingStandard\Configuration\ConfigurationFileLoader;

final class ChangedFilesDetector implements ChangedFilesDetectorInterface
{
    /**
     * @var string
     */
    private const CONFIGURATION_HASH_KEY = 'configuration_hash';

    /**
     * @var Cache
     */
    private $cache;

    public function __construct(CacheFactory $cacheFactory, ConfigurationFileLoader $configurationFileLoader)
    {
        $this->cache = $cacheFactory->create();
        $this->storeConfigurationDataHash($configurationFileLoader->load());
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
        return hash_file('md5', $filePath);
    }

    /**
     * @param mixed[] $configuration
     */
    private function storeConfigurationDataHash(array $configuration): void
    {
        $configurationHash = $this->calculateConfigurationHash($configuration);

        $this->invalidateCacheIfConfigurationChanged($configurationHash);
        $this->cache->save(self::CONFIGURATION_HASH_KEY, $configurationHash);
    }

    /**
     * @param mixed[] $configuration
     */
    private function calculateConfigurationHash(array $configuration): string
    {
        return md5(serialize($configuration));
    }

    private function invalidateCacheIfConfigurationChanged(string $configurationHash): void
    {
        $oldConfigurationHash = $this->cache->load(self::CONFIGURATION_HASH_KEY);
        if ($configurationHash !== $oldConfigurationHash) {
            $this->clearCache();
        }
    }
}
