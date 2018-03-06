<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection;

use Nette\Utils\Strings;
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\CollectorCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\CustomSourceProviderDefinitionCompilerPass;
use Symplify\EasyCodingStandard\Exception\Configuration\DeprecatedConfigException;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireSinglyImplementedCompilerPass;
use Symplify\PackageBuilder\HttpKernel\AbstractCliKernel;
use Symplify\PackageBuilder\Neon\Loader\NeonLoader;
use Symplify\PackageBuilder\Neon\NeonLoaderAwareKernelTrait;

final class EasyCodingStandardKernel extends AbstractCliKernel
{
    use NeonLoaderAwareKernelTrait;

    /**
     * @deprecated
     * @var string
     */
    private const RENAMED_CONFIG_PATTERN = '#- (?<oldConfigName>(?<configNameWithoutSuffix>[a-z/-]+)-checkers.neon)#';

    /**
     * @deprecated
     * @var string
     */
    private const PHP54_CONFIG_PATTERN = '#- (?<oldConfigName>[a-z/-]+php54.neon)#';

    /**
     * @deprecated
     * @var string
     */
    private const SPACES_CONFIG_PATTERN = '#- (?<oldConfigName>[a-z/-]+config/spaces.neon)#';

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
            $this->informAboutDeprecatedConfigs($this->configFile);
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

    /**
     * Make upgrade more pleasant
     *
     * @deprecated
     */
    private function informAboutDeprecatedConfigs(string $configFile): void
    {
        $fileContent = file_get_contents($configFile);

        $this->informAboutDeprecatedCheckersSuffixConfig($configFile, $fileContent);
        $this->informAboutDeprecatedPhp54Config($configFile, $fileContent);
        $this->informAboutDeprecatedSpacesConfig($configFile, $fileContent);
    }

    /**
     * @deprecated
     */
    private function informAboutDeprecatedCheckersSuffixConfig(string $configFile, string $fileContent): void
    {
        $matches = Strings::matchAll($fileContent, self::RENAMED_CONFIG_PATTERN);
        foreach ($matches as $match) {
            throw new DeprecatedConfigException(sprintf(
                'Config file "%s" contains old file name%s"%s" with removed "-checkers" suffix. Use %s"%s" instead.',
                $configFile,
                PHP_EOL,
                $match['oldConfigName'],
                PHP_EOL,
                $match['configNameWithoutSuffix'] . '.neon'
            ));
        }
    }

    /**
     * @deprecated
     */
    private function informAboutDeprecatedPhp54Config(string $configFile, string $fileContent): void
    {
        $match = Strings::match($fileContent, self::PHP54_CONFIG_PATTERN);
        if ($match) {
            throw new DeprecatedConfigException(sprintf(
                'Config file "%s" contains old file "%s".%sRemove it and use %s"%s" directly or import %s"%s" config.',
                $configFile,
                $match['oldConfigName'],
                PHP_EOL . PHP_EOL,
                PHP_EOL,
                ArraySyntaxFixer::class,
                PHP_EOL,
                'vendor/symplify/easy-coding-standard/config/common/array.neon'
            ));
        }
    }

    /**
     * @deprecated
     */
    private function informAboutDeprecatedSpacesConfig(string $configFile, string $fileContent): void
    {
        $match = Strings::match($fileContent, self::SPACES_CONFIG_PATTERN);
        if ($match) {
            throw new DeprecatedConfigException(sprintf(
                'Config file "%s" contains old file "%s".%s' .
                    'Just rename it to %s"%s" or if you have%s"%s", just remove it.',
                $configFile,
                $match['oldConfigName'],
                PHP_EOL . PHP_EOL,
                PHP_EOL,
                'vendor/symplify/easy-coding-standard/config/common/spaces.neon',
                PHP_EOL,
                'vendor/symplify/easy-coding-standard/config/common.neon'
            ));
        }
    }
}
