<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\ConfigTransformer\FileSystem;

use ConfigTransformer20210601\Symfony\Component\Console\Style\SymfonyStyle;
use ConfigTransformer20210601\Symplify\ConfigTransformer\Configuration\Configuration;
use ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\ConvertedContent;
use ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileSystem;
final class ConfigFileDumper
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
     * @var SmartFileSystem
     */
    private $smartFileSystem;
    public function __construct(\ConfigTransformer20210601\Symplify\ConfigTransformer\Configuration\Configuration $configuration, \ConfigTransformer20210601\Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle, \ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileSystem $smartFileSystem)
    {
        $this->configuration = $configuration;
        $this->symfonyStyle = $symfonyStyle;
        $this->smartFileSystem = $smartFileSystem;
    }
    /**
     * @return void
     */
    public function dumpFile(\ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\ConvertedContent $convertedContent)
    {
        $originalFilePathWithoutSuffix = $convertedContent->getOriginalFilePathWithoutSuffix();
        $newFileRealPath = $originalFilePathWithoutSuffix . '.' . $this->configuration->getOutputFormat();
        $relativeFilePath = $this->getRelativePathOfNonExistingFile($newFileRealPath);
        if ($this->configuration->isDryRun()) {
            $message = \sprintf('File "%s" would be dumped (is --dry-run)', $relativeFilePath);
            $this->symfonyStyle->note($message);
            return;
        }
        $this->smartFileSystem->dumpFile($newFileRealPath, $convertedContent->getConvertedContent());
        $message = \sprintf('File "%s" was dumped', $relativeFilePath);
        $this->symfonyStyle->note($message);
    }
    private function getRelativePathOfNonExistingFile(string $newFilePath) : string
    {
        $relativeFilePath = $this->smartFileSystem->makePathRelative($newFilePath, \getcwd());
        return \rtrim($relativeFilePath, '/');
    }
}
