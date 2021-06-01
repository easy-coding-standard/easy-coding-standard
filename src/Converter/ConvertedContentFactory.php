<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\ConfigTransformer\Converter;

use ConfigTransformer20210601\Symfony\Component\Console\Style\SymfonyStyle;
use ConfigTransformer20210601\Symplify\ConfigTransformer\Configuration\Configuration;
use ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\ConvertedContent;
use ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileInfo;
final class ConvertedContentFactory
{
    /**
     * @var Configuration
     */
    private $configuration;
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @var ConfigFormatConverter
     */
    private $configFormatConverter;
    public function __construct(\ConfigTransformer20210601\Symplify\ConfigTransformer\Configuration\Configuration $configuration, \ConfigTransformer20210601\Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle, \ConfigTransformer20210601\Symplify\ConfigTransformer\Converter\ConfigFormatConverter $configFormatConverter)
    {
        $this->configuration = $configuration;
        $this->symfonyStyle = $symfonyStyle;
        $this->configFormatConverter = $configFormatConverter;
    }
    /**
     * @param SmartFileInfo[] $fileInfos
     * @return ConvertedContent[]
     */
    public function createFromFileInfos(array $fileInfos) : array
    {
        $convertedContentFromFileInfo = [];
        foreach ($fileInfos as $fileInfo) {
            $message = \sprintf('Processing "%s" file', $fileInfo->getRelativeFilePathFromCwd());
            $this->symfonyStyle->note($message);
            $convertedContent = $this->configFormatConverter->convert($fileInfo, $this->configuration->getInputFormat(), $this->configuration->getOutputFormat());
            $convertedContentFromFileInfo[] = new \ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\ConvertedContent($convertedContent, $fileInfo);
        }
        return $convertedContentFromFileInfo;
    }
}
