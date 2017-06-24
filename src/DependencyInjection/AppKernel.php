<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection;

use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\EasyCodingStandard\Configuration\Loader\NeonLoader;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\CollectorCompilerPass;

final class AppKernel extends Kernel
{
    /**
     * @var string
     */
    private const CONFIG_NAME = 'easy-coding-standard.neon';

    /**
     * @var string
     */
    private $customConfig;

    /**
     * @var bool
     */
    private $autoloadLocalConfig = true;

    public function __construct(?string $customConfig = '', bool $autoloadLocalConfig = true)
    {
        $this->customConfig = $customConfig;
        // randomize name to prevent using container same cache for custom configs (e.g. ErrorCollector test)
        parent::__construct(random_int(1, 1000), true);
        $this->autoloadLocalConfig = $autoloadLocalConfig;
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../config/services.yml');

        if ($this->autoloadLocalConfig && $localConfig = $this->getConfigPath()) {
            $loader->load($localConfig);
        }

        if ($this->customConfig && file_exists($this->customConfig)) {
            $loader->load($this->customConfig);
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
            new CheckersBundle
        ];
    }

    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new CollectorCompilerPass);
    }

    protected function getContainerLoader(ContainerInterface $container): DelegatingLoader
    {
        /** @var DelegatingLoader $delegationLoader */
        $delegationLoader = parent::getContainerLoader($container);

        /** @var LoaderResolver $resolver */
        $resolver = $delegationLoader->getResolver();
        $resolver->addLoader(new NeonLoader($container));

        return $delegationLoader;
    }

    /**
     * @return string|false
     */
    private function getConfigPath()
    {
        $possibleConfigPaths = [
            getcwd() . '/' . self::CONFIG_NAME,
            __DIR__ . '/../../' . self::CONFIG_NAME,
            __DIR__ . '/../../../../' . self::CONFIG_NAME,
        ];

        foreach ($possibleConfigPaths as $possibleConfigPath) {
            if (file_exists($possibleConfigPath)) {
                return $possibleConfigPath;
            }
        }

        return false;
    }
}
