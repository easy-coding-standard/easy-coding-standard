<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\CollectorCompilerPass;
use Symplify\PackageBuilder\HttpKernel\AbstractCliKernel;

final class AppKernel extends AbstractCliKernel
{
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
        $this->autoloadLocalConfig = $autoloadLocalConfig;
        parent::__construct();
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../config/services.yml');

        if ($this->autoloadLocalConfig) {
            $this->registerLocalConfig($loader, 'easy-coding-standard.neon');
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
            new CheckersBundle,
        ];
    }

    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new CollectorCompilerPass);
    }
}
