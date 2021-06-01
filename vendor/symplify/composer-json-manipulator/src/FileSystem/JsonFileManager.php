<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\ComposerJsonManipulator\FileSystem;

use ConfigTransformer20210601\Nette\Utils\Json;
use ConfigTransformer20210601\Symplify\ComposerJsonManipulator\Json\JsonCleaner;
use ConfigTransformer20210601\Symplify\ComposerJsonManipulator\Json\JsonInliner;
use ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use ConfigTransformer20210601\Symplify\PackageBuilder\Configuration\StaticEolConfiguration;
use ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileInfo;
use ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileSystem;
/**
 * @see \Symplify\MonorepoBuilder\Tests\FileSystem\JsonFileManager\JsonFileManagerTest
 */
final class JsonFileManager
{
    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;
    /**
     * @var JsonCleaner
     */
    private $jsonCleaner;
    /**
     * @var JsonInliner
     */
    private $jsonInliner;
    /**
     * @var mixed[]
     */
    private $cachedJSONFiles = [];
    public function __construct(\ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileSystem $smartFileSystem, \ConfigTransformer20210601\Symplify\ComposerJsonManipulator\Json\JsonCleaner $jsonCleaner, \ConfigTransformer20210601\Symplify\ComposerJsonManipulator\Json\JsonInliner $jsonInliner)
    {
        $this->smartFileSystem = $smartFileSystem;
        $this->jsonCleaner = $jsonCleaner;
        $this->jsonInliner = $jsonInliner;
    }
    /**
     * @return mixed[]
     */
    public function loadFromFileInfo(\ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo) : array
    {
        $realPath = $smartFileInfo->getRealPath();
        if (!isset($this->cachedJSONFiles[$realPath])) {
            $this->cachedJSONFiles[$realPath] = \ConfigTransformer20210601\Nette\Utils\Json::decode($smartFileInfo->getContents(), \ConfigTransformer20210601\Nette\Utils\Json::FORCE_ARRAY);
        }
        return $this->cachedJSONFiles[$realPath];
    }
    /**
     * @return array<string, mixed>
     */
    public function loadFromFilePath(string $filePath) : array
    {
        $fileContent = $this->smartFileSystem->readFile($filePath);
        return \ConfigTransformer20210601\Nette\Utils\Json::decode($fileContent, \ConfigTransformer20210601\Nette\Utils\Json::FORCE_ARRAY);
    }
    /**
     * @param mixed[] $json
     */
    public function printJsonToFileInfo(array $json, \ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo) : string
    {
        $jsonString = $this->encodeJsonToFileContent($json);
        $this->smartFileSystem->dumpFile($smartFileInfo->getPathname(), $jsonString);
        return $jsonString;
    }
    public function printComposerJsonToFilePath(\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJson $composerJson, string $filePath) : string
    {
        $jsonString = $this->encodeJsonToFileContent($composerJson->getJsonArray());
        $this->smartFileSystem->dumpFile($filePath, $jsonString);
        return $jsonString;
    }
    /**
     * @param mixed[] $json
     */
    public function encodeJsonToFileContent(array $json) : string
    {
        // Empty arrays may lead to bad encoding since we can't be sure whether they need to be arrays or objects.
        $json = $this->jsonCleaner->removeEmptyKeysFromJsonArray($json);
        $jsonContent = \ConfigTransformer20210601\Nette\Utils\Json::encode($json, \ConfigTransformer20210601\Nette\Utils\Json::PRETTY) . \ConfigTransformer20210601\Symplify\PackageBuilder\Configuration\StaticEolConfiguration::getEolChar();
        return $this->jsonInliner->inlineSections($jsonContent);
    }
}
