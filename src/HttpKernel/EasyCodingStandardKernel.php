<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\HttpKernel;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\EasyCodingStandard\ChangedFilesDetector\CompilerPass\AddSysGetTempDirParameterCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\ConflictingCheckersCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\CustomSourceProviderDefinitionCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\FixerWhitespaceConfigCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\RemoveExcludedCheckersCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\RemoveMutualCheckersCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\DelegatingLoaderFactory;
use Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutoBindParametersCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutoReturnFactoryCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireInterfacesCompilerPass;

final class EasyCodingStandardKernel extends Kernel implements ExtraConfigAwareKernelInterface
{
    /**
     * @var string[]
     */
    private $configs = [];

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../config/config.yaml');

        foreach ($this->configs as $config) {
            $loader->load($config);
        }
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/easy_coding_standard';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/easy_coding_standard_logs';
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        return [];
    }

    /**
     * @param string[] $configs
     */
    public function setConfigs(array $configs): void
    {
        $this->configs = $configs;
    }

    /**
     * Order matters!
     */
    protected function build(ContainerBuilder $containerBuilder): void
    {
        // needs to be first, since it's adding new service definitions
        $containerBuilder->addCompilerPass(new AutoReturnFactoryCompilerPass());

        // cleanup
        $containerBuilder->addCompilerPass(new RemoveExcludedCheckersCompilerPass());
        $containerBuilder->addCompilerPass(new RemoveMutualCheckersCompilerPass());

        // autowire
        $containerBuilder->addCompilerPass(new AutowireInterfacesCompilerPass([
            FixerInterface::class,
            Sniff::class,
        ]));
        $containerBuilder->addCompilerPass(new AutowireArrayParameterCompilerPass());

        // exceptions
        $containerBuilder->addCompilerPass(new ConflictingCheckersCompilerPass());

        // parameters
        $containerBuilder->addCompilerPass(new AddSysGetTempDirParameterCompilerPass());
        $containerBuilder->addCompilerPass(new AutoBindParametersCompilerPass());

        // method calls
        $containerBuilder->addCompilerPass(new FixerWhitespaceConfigCompilerPass());
        $containerBuilder->addCompilerPass(new CustomSourceProviderDefinitionCompilerPass());
    }

    /**
     * @param ContainerInterface|ContainerBuilder $container
     */
    protected function getContainerLoader(ContainerInterface $container): DelegatingLoader
    {
        return (new DelegatingLoaderFactory())->createFromContainerBuilderAndKernel($container, $this);
    }
}
