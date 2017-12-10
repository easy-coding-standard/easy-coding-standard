<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection;

use Nette\Utils\Strings;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\CollectorCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\CustomSourceProviderDefinitionCompilerPass;
use Symplify\PackageBuilder\HttpKernel\AbstractCliKernel;
use Symplify\PackageBuilder\Neon\NeonLoaderAwareKernelTrait;

final class AppKernel extends AbstractCliKernel
{
    use NeonLoaderAwareKernelTrait;

    /**
     * @var string
     */
    private const RENAMED_CONFIG_PATTERN = '#- (?<oldConfigName>(?<configNameWithoutSuffix>[a-z/-]+)-checkers.neon)#';

    /**
     * @var null|string
     */
    private $configFile;

    public function __construct(?string $configFile = null)
    {
        $this->configFile = $configFile;
        parent::__construct();
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../config/services.yml');
        if ($this->configFile) {
            $this->informAboutDeprecatedCheckersSuffix($this->configFile);
            $loader->load($this->configFile, ['parameters', 'checkers', 'includes', 'services']);
        }
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/_easy_coding_standard';
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [
            new CheckersBundle(),
        ];
    }

    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new CollectorCompilerPass());
        $containerBuilder->addCompilerPass(new CustomSourceProviderDefinitionCompilerPass());
    }

    /**
     * Make upgrade more pleasant
     */
    private function informAboutDeprecatedCheckersSuffix(string $configFile): void
    {
        $fileContent = file_get_contents($configFile);
        $matches = Strings::matchAll($fileContent, self::RENAMED_CONFIG_PATTERN);

        foreach ($matches as $match) {
            /* DeprecatedConfigNameException*/
            throw new DeprecatedConfigNameException(sprintf(
                'Config file "%s" contains old file name%s"%s" with removed "-checkers" suffix. Use %s"%s" instead.',
                $configFile,
                PHP_EOL,
                $match['oldConfigName'],
                PHP_EOL,
                $match['configNameWithoutSuffix'] . '.neon'
            ));
        }
    }
}
