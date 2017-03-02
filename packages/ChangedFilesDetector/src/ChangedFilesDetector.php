<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ChangedFilesDetector;

use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;
use Nette\Utils\FileSystem;
use Symplify\EasyCodingStandard\Configuration\ConfigurationFileLoader;

final class ChangedFilesDetector
{
    /**
     * @var string
     */
    const CONFIGURATION_HASH_KEY = 'configuration_hash';

    /**
     * @var Cache
     */
    private $cache;

    // todo: introduce an interface, that return a "string" as unique signature
    // todo: add cache factory
    public function __construct(Cache $cache, ConfigurationFileLoader $configurationFileLoader)
    {
        $tempDirectory = sys_get_temp_dir(). '/_changed_files_detector';
        FileSystem::createDir($tempDirectory);

        $this->cache = new Cache(new FileStorage($tempDirectory));
        $this->storeConfigurationDataHash($configurationFileLoader->load());
    }

    public function addFile(string $filePath): void
    {
        $hash = $this->hashFile($filePath);
        $this->cache->save($filePath, $hash);
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

    private function hashFile(string $filePath): string
    {
        return hash_file('md5',$filePath);
    }

    private function storeConfigurationDataHash(array $configuration): void
    {
        $configurationHash = $this->calculateConfigurationHash($configuration);

        $this->invalidateCacheIfConfigurationChanged($configurationHash);
        $this->cache->save(self::CONFIGURATION_HASH_KEY, $configurationHash);
    }

    private function calculateConfigurationHash(array $configuration): string
    {
        return md5(serialize($configuration));
    }

    private function invalidateCacheIfConfigurationChanged(string $configurationHash): void
    {
        $oldConfigurationHash = $this->cache->load(self::CONFIGURATION_HASH_KEY);
        if ($configurationHash !== $oldConfigurationHash) {
            $this->cleanCache();
        }
    }

    private function cleanCache(): void
    {
        $this->cache->clean([Cache::ALL => true]);
    }
}
