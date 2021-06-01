<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\ConfigTransformer\Converter;

use ConfigTransformer20210601\Symfony\Component\Console\Style\SymfonyStyle;
use ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\ConvertedContent;
use ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileInfo;
final class ConvertedContentFactory
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @var ConfigFormatConverter
     */
    private $configFormatConverter;
    public function __construct(\ConfigTransformer20210601\Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle, \ConfigTransformer20210601\Symplify\ConfigTransformer\Converter\ConfigFormatConverter $configFormatConverter)
    {
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
            $convertedContent = $this->configFormatConverter->convert($fileInfo);
            $convertedContentFromFileInfo[] = new \ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\ConvertedContent($convertedContent, $fileInfo);
        }
        return $convertedContentFromFileInfo;
    }
}
