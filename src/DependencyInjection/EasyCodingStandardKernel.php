<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection;

use Nette\Utils\Strings;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Yaml\Yaml;
use Symplify\EasyCodingStandard\ChangedFilesDetector\CompilerPass\DetectParametersCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\AutowireCheckersCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\CollectorCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\ConflictingCheckersCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\CustomSourceProviderDefinitionCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\FixerWhitespaceConfigCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\RemoveExcludedCheckersCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\RemoveMutualCheckersCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutoBindParametersCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireSinglyImplementedCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\PublicForTestsCompilerPass;
use Symplify\PackageBuilder\HttpKernel\SimpleKernelTrait;

final class EasyCodingStandardKernel extends Kernel
{
    use SimpleKernelTrait;

    /**
     * @var string[]
     */
    private $extraConfigFiles = [];

    /**
     * @param string[] $configFiles
     */
    public function __construct(array $configFiles = [])
    {
        $this->extraConfigFiles = $configFiles;

        $configFilesHash = md5(serialize($configFiles));

        // debug: require to invalidate container on service files change
        parent::__construct('ecs_' . $configFilesHash, true);
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../config/config.yml');

        foreach ($this->extraConfigFiles as $configFile) {
            $loader->load($configFile);
        }
    }

    /**
     * Order matters!
     */
    protected function build(ContainerBuilder $containerBuilder): void
    {
        foreach ($this->extraConfigFiles as $extraConfigFile) {
            // not a root config
            if (! Strings::match($extraConfigFile, '#(ecs|easy-coding-standard)\.y(a)?ml$#')) {
                continue;
            }

            // get root skip parameters, for unused skipper
            $parsedRootConfig = Yaml::parseFile($extraConfigFile);
            if (isset($parsedRootConfig['parameters']['skip'])) {
                $containerBuilder->setParameter('root_skip', $parsedRootConfig['parameters']['skip']);
            }
        }

        // cleanup
        $containerBuilder->addCompilerPass(new RemoveExcludedCheckersCompilerPass());
        $containerBuilder->addCompilerPass(new RemoveMutualCheckersCompilerPass());

        // autowire
        $containerBuilder->addCompilerPass(new AutowireCheckersCompilerPass());

        // exceptions
        $containerBuilder->addCompilerPass(new ConflictingCheckersCompilerPass());

        // tests
        $containerBuilder->addCompilerPass(new PublicForTestsCompilerPass());

        // parameters
        $containerBuilder->addCompilerPass(new DetectParametersCompilerPass());
        $containerBuilder->addCompilerPass(new AutoBindParametersCompilerPass());

        // method calls
        $containerBuilder->addCompilerPass(new FixerWhitespaceConfigCompilerPass());
        $containerBuilder->addCompilerPass(new CollectorCompilerPass());
        $containerBuilder->addCompilerPass(new CustomSourceProviderDefinitionCompilerPass());
        $containerBuilder->addCompilerPass(new AutowireSinglyImplementedCompilerPass());
    }

    /**
     * @param ContainerInterface|ContainerBuilder $container
     */
    protected function getContainerLoader(ContainerInterface $container): DelegatingLoader
    {
        return (new DelegatingLoaderFactory())->createFromContainerBuilderAndKernel($container, $this);
    }
}
