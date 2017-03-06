<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration;

use Nette\DI\Config\Loader;

final class ConfigurationFileLoader
{
    /**
     * @var string
     */
    private const CONFIGURATION_FILE = 'easy-coding-standard.neon';

    /**
     * @var string
     */
    private $configurationFile;

    /**
     * @var Loader
     */
    private $neonLoader;

    public function __construct(?string $configurationFile = null, Loader $neonLoader)
    {
        $this->setConfigurationFile($configurationFile);
        $this->neonLoader = $neonLoader;
    }

    /**
     * @return mixed[]
     */
    public function load(): array
    {
        return $this->neonLoader->load($this->configurationFile);
    }

    private function setConfigurationFile(?string $configurationFile = null): void
    {
        $this->configurationFile = $configurationFile ?: getcwd() . DIRECTORY_SEPARATOR . self::CONFIGURATION_FILE;
    }
}
