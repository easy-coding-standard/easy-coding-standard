<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\ConfigTransformer\Converter;

use ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerBuilder;
use ConfigTransformer20210601\Symfony\Component\Yaml\Yaml;
use ConfigTransformer20210601\Symplify\ConfigTransformer\Collector\XmlImportCollector;
use ConfigTransformer20210601\Symplify\ConfigTransformer\ConfigLoader;
use ConfigTransformer20210601\Symplify\ConfigTransformer\DependencyInjection\ContainerBuilderCleaner;
use ConfigTransformer20210601\Symplify\ConfigTransformer\DumperFactory;
use ConfigTransformer20210601\Symplify\ConfigTransformer\DumperFomatter\YamlDumpFormatter;
use ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Format;
use ConfigTransformer20210601\Symplify\PackageBuilder\Exception\NotImplementedYetException;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\Provider\CurrentFilePathProvider;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\YamlToPhpConverter;
use ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileInfo;
use ConfigTransformer20210601\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
final class ConfigFormatConverter
{
    /**
     * @var ConfigLoader
     */
    private $configLoader;
    /**
     * @var DumperFactory
     */
    private $dumperFactory;
    /**
     * @var ContainerBuilderCleaner
     */
    private $containerBuilderCleaner;
    /**
     * @var YamlDumpFormatter
     */
    private $yamlDumpFormatter;
    /**
     * @var YamlToPhpConverter
     */
    private $yamlToPhpConverter;
    /**
     * @var CurrentFilePathProvider
     */
    private $currentFilePathProvider;
    /**
     * @var XmlImportCollector
     */
    private $xmlImportCollector;
    public function __construct(\ConfigTransformer20210601\Symplify\ConfigTransformer\ConfigLoader $configLoader, \ConfigTransformer20210601\Symplify\ConfigTransformer\DumperFactory $dumperFactory, \ConfigTransformer20210601\Symplify\ConfigTransformer\DependencyInjection\ContainerBuilderCleaner $containerBuilderCleaner, \ConfigTransformer20210601\Symplify\ConfigTransformer\DumperFomatter\YamlDumpFormatter $yamlDumpFormatter, \ConfigTransformer20210601\Symplify\PhpConfigPrinter\YamlToPhpConverter $yamlToPhpConverter, \ConfigTransformer20210601\Symplify\PhpConfigPrinter\Provider\CurrentFilePathProvider $currentFilePathProvider, \ConfigTransformer20210601\Symplify\ConfigTransformer\Collector\XmlImportCollector $xmlImportCollector)
    {
        $this->configLoader = $configLoader;
        $this->dumperFactory = $dumperFactory;
        $this->containerBuilderCleaner = $containerBuilderCleaner;
        $this->yamlDumpFormatter = $yamlDumpFormatter;
        $this->yamlToPhpConverter = $yamlToPhpConverter;
        $this->currentFilePathProvider = $currentFilePathProvider;
        $this->xmlImportCollector = $xmlImportCollector;
    }
    public function convert(\ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo, string $inputFormat, string $outputFormat) : string
    {
        $this->currentFilePathProvider->setFilePath($smartFileInfo->getRealPath());
        $containerBuilderAndFileContent = $this->configLoader->createAndLoadContainerBuilderFromFileInfo($smartFileInfo);
        $containerBuilder = $containerBuilderAndFileContent->getContainerBuilder();
        if ($outputFormat === \ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Format::YAML) {
            $dumpedYaml = $this->dumpContainerBuilderToYaml($containerBuilder);
            return $this->decorateWithCollectedXmlImports($dumpedYaml);
        }
        if ($outputFormat === \ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Format::PHP) {
            if ($inputFormat === \ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Format::YAML) {
                $dumpedYaml = $containerBuilderAndFileContent->getFileContent();
                $dumpedYaml = $this->decorateWithCollectedXmlImports($dumpedYaml);
                return $this->yamlToPhpConverter->convert($dumpedYaml);
            }
            if ($inputFormat === \ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Format::XML) {
                $dumpedYaml = $this->dumpContainerBuilderToYaml($containerBuilder);
                $dumpedYaml = $this->decorateWithCollectedXmlImports($dumpedYaml);
                return $this->yamlToPhpConverter->convert($dumpedYaml);
            }
        }
        $message = \sprintf('Converting from "%s" to "%s" it not support yet', $inputFormat, $outputFormat);
        throw new \ConfigTransformer20210601\Symplify\PackageBuilder\Exception\NotImplementedYetException($message);
    }
    private function dumpContainerBuilderToYaml(\ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder) : string
    {
        $yamlDumper = $this->dumperFactory->createFromContainerBuilderAndOutputFormat($containerBuilder, \ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Format::YAML);
        $this->containerBuilderCleaner->cleanContainerBuilder($containerBuilder);
        $content = $yamlDumper->dump();
        if (!\is_string($content)) {
            throw new \ConfigTransformer20210601\Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
        }
        return $this->yamlDumpFormatter->format($content);
    }
    private function decorateWithCollectedXmlImports(string $dumpedYaml) : string
    {
        $collectedXmlImports = $this->xmlImportCollector->provide();
        if ($collectedXmlImports === []) {
            return $dumpedYaml;
        }
        $yamlArray = \ConfigTransformer20210601\Symfony\Component\Yaml\Yaml::parse($dumpedYaml, \ConfigTransformer20210601\Symfony\Component\Yaml\Yaml::PARSE_CUSTOM_TAGS);
        $yamlArray['imports'] = \array_merge($yamlArray['imports'] ?? [], $collectedXmlImports);
        return \ConfigTransformer20210601\Symfony\Component\Yaml\Yaml::dump($yamlArray, 10, 4, \ConfigTransformer20210601\Symfony\Component\Yaml\Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);
    }
}
