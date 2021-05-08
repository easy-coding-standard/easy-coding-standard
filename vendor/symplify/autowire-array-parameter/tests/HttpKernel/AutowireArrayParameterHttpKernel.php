<?php

declare (strict_types=1);
namespace Symplify\AutowireArrayParameter\Tests\HttpKernel;

use ECSPrefix20210508\Symfony\Component\Config\Loader\LoaderInterface;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210508\Symfony\Component\HttpKernel\Bundle\BundleInterface;
use ECSPrefix20210508\Symfony\Component\HttpKernel\Kernel;
use Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface;
final class AutowireArrayParameterHttpKernel extends \ECSPrefix20210508\Symfony\Component\HttpKernel\Kernel implements \Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface
{
    /**
     * @var string[]
     */
    private $configs = [];
    public function __construct()
    {
        // to invoke container override for test re-run
        parent::__construct('dev' . \random_int(0, 10000), \true);
    }
    public function registerContainerConfiguration(\ECSPrefix20210508\Symfony\Component\Config\Loader\LoaderInterface $loader) : void
    {
        $loader->load(__DIR__ . '/../config/autowire_array_parameter.php');
        foreach ($this->configs as $config) {
            $loader->load($config);
        }
    }
    public function getCacheDir() : string
    {
        return \sys_get_temp_dir() . '/autowire_array_parameter_test';
    }
    public function getLogDir() : string
    {
        return \sys_get_temp_dir() . '/autowire_array_parameter_test_log';
    }
    /**
     * @return BundleInterface[]
     */
    public function registerBundles() : iterable
    {
        return [];
    }
    /**
     * @param string[] $configs
     */
    public function setConfigs(array $configs) : void
    {
        $this->configs = $configs;
    }
    protected function build(\ECSPrefix20210508\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder) : void
    {
        $containerBuilder->addCompilerPass(new \Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass());
    }
}
