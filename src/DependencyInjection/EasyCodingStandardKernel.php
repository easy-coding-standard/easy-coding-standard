<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\CollectorCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\CustomSourceProviderDefinitionCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireSinglyImplementedCompilerPass;
use Symplify\PackageBuilder\HttpKernel\AbstractCliKernel;
use Symplify\PackageBuilder\Neon\Loader\NeonLoader;
use Symplify\PackageBuilder\Neon\NeonLoaderAwareKernelTrait;

final class EasyCodingStandardKernel extends AbstractCliKernel
{
    use NeonLoaderAwareKernelTrait;

    /**
     * @var null|string
     */
    private $configFile;

    public function __construct(?string $configFile = null)
    {
        $this->configFile = $configFile;
        parent::__construct();
    }

    /**
     * @param NeonLoader $loader
     */
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../config/services.yml');
        if ($this->configFile) {
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
        $containerBuilder->addCompilerPass(new AutowireSinglyImplementedCompilerPass());
    }
}
