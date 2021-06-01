<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\ConfigTransformer;

use ConfigTransformer20210601\Nette\Utils\Strings;
use ConfigTransformer20210601\Symfony\Component\Config\FileLocator;
use ConfigTransformer20210601\Symfony\Component\Config\Loader\DelegatingLoader;
use ConfigTransformer20210601\Symfony\Component\Config\Loader\Loader;
use ConfigTransformer20210601\Symfony\Component\Config\Loader\LoaderResolver;
use ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerBuilder;
use ConfigTransformer20210601\Symfony\Component\DependencyInjection\Loader\GlobFileLoader;
use ConfigTransformer20210601\Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use ConfigTransformer20210601\Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use ConfigTransformer20210601\Symplify\ConfigTransformer\DependencyInjection\ExtensionFaker;
use ConfigTransformer20210601\Symplify\ConfigTransformer\DependencyInjection\Loader\CheckerTolerantYamlFileLoader;
use ConfigTransformer20210601\Symplify\ConfigTransformer\DependencyInjection\LoaderFactory\IdAwareXmlFileLoaderFactory;
use ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\ContainerBuilderAndFileContent;
use ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Format;
use ConfigTransformer20210601\Symplify\PackageBuilder\Exception\NotImplementedYetException;
use ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileInfo;
use ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileSystem;
final class ConfigLoader
{
    /**
     * @see https://regex101.com/r/Mnd9vH/1
     * @var string
     */
    const PHP_CONST_REGEX = '#\\!php\\/const\\:( )?#';
    /**
     * @var IdAwareXmlFileLoaderFactory
     */
    private $idAwareXmlFileLoaderFactory;
    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;
    /**
     * @var ExtensionFaker
     */
    private $extensionFaker;
    public function __construct(\ConfigTransformer20210601\Symplify\ConfigTransformer\DependencyInjection\LoaderFactory\IdAwareXmlFileLoaderFactory $idAwareXmlFileLoaderFactory, \ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileSystem $smartFileSystem, \ConfigTransformer20210601\Symplify\ConfigTransformer\DependencyInjection\ExtensionFaker $extensionFaker)
    {
        $this->idAwareXmlFileLoaderFactory = $idAwareXmlFileLoaderFactory;
        $this->smartFileSystem = $smartFileSystem;
        $this->extensionFaker = $extensionFaker;
    }
    public function createAndLoadContainerBuilderFromFileInfo(\ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo) : \ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\ContainerBuilderAndFileContent
    {
        $containerBuilder = new \ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerBuilder();
        $delegatingLoader = $this->createLoaderBySuffix($containerBuilder, $smartFileInfo->getSuffix());
        $fileRealPath = $smartFileInfo->getRealPath();
        // correct old syntax of tags so we can parse it
        $content = $smartFileInfo->getContents();
        if (\in_array($smartFileInfo->getSuffix(), [\ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Format::YML, \ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Format::YAML], \true)) {
            $content = \ConfigTransformer20210601\Nette\Utils\Strings::replace($content, self::PHP_CONST_REGEX, '!php/const ');
            if ($content !== $smartFileInfo->getContents()) {
                $fileRealPath = \sys_get_temp_dir() . '/_migrify_config_tranformer_clean_yaml/' . $smartFileInfo->getFilename();
                $this->smartFileSystem->dumpFile($fileRealPath, $content);
            }
            $this->extensionFaker->fakeInContainerBuilder($containerBuilder, $content);
        }
        $delegatingLoader->load($fileRealPath);
        return new \ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\ContainerBuilderAndFileContent($containerBuilder, $content);
    }
    private function createLoaderBySuffix(\ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, string $suffix) : \ConfigTransformer20210601\Symfony\Component\Config\Loader\DelegatingLoader
    {
        if ($suffix === \ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Format::XML) {
            $idAwareXmlFileLoader = $this->idAwareXmlFileLoaderFactory->createFromContainerBuilder($containerBuilder);
            return $this->wrapToDelegatingLoader($idAwareXmlFileLoader, $containerBuilder);
        }
        if (\in_array($suffix, [\ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Format::YML, \ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Format::YAML], \true)) {
            $yamlFileLoader = new \ConfigTransformer20210601\Symfony\Component\DependencyInjection\Loader\YamlFileLoader($containerBuilder, new \ConfigTransformer20210601\Symfony\Component\Config\FileLocator());
            return $this->wrapToDelegatingLoader($yamlFileLoader, $containerBuilder);
        }
        if ($suffix === \ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Format::PHP) {
            $phpFileLoader = new \ConfigTransformer20210601\Symfony\Component\DependencyInjection\Loader\PhpFileLoader($containerBuilder, new \ConfigTransformer20210601\Symfony\Component\Config\FileLocator());
            return $this->wrapToDelegatingLoader($phpFileLoader, $containerBuilder);
        }
        throw new \ConfigTransformer20210601\Symplify\PackageBuilder\Exception\NotImplementedYetException($suffix);
    }
    private function wrapToDelegatingLoader(\ConfigTransformer20210601\Symfony\Component\Config\Loader\Loader $loader, \ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder) : \ConfigTransformer20210601\Symfony\Component\Config\Loader\DelegatingLoader
    {
        $globFileLoader = new \ConfigTransformer20210601\Symfony\Component\DependencyInjection\Loader\GlobFileLoader($containerBuilder, new \ConfigTransformer20210601\Symfony\Component\Config\FileLocator());
        $phpFileLoader = new \ConfigTransformer20210601\Symfony\Component\DependencyInjection\Loader\PhpFileLoader($containerBuilder, new \ConfigTransformer20210601\Symfony\Component\Config\FileLocator());
        $checkerTolerantYamlFileLoader = new \ConfigTransformer20210601\Symplify\ConfigTransformer\DependencyInjection\Loader\CheckerTolerantYamlFileLoader($containerBuilder, new \ConfigTransformer20210601\Symfony\Component\Config\FileLocator());
        return new \ConfigTransformer20210601\Symfony\Component\Config\Loader\DelegatingLoader(new \ConfigTransformer20210601\Symfony\Component\Config\Loader\LoaderResolver([$globFileLoader, $phpFileLoader, $checkerTolerantYamlFileLoader, $loader]));
    }
}
