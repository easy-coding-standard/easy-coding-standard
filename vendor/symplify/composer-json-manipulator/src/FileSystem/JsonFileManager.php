<?php

namespace ECSPrefix20210514\Symplify\ComposerJsonManipulator\FileSystem;

use ECSPrefix20210514\Nette\Utils\Json;
use ECSPrefix20210514\Symplify\ComposerJsonManipulator\Json\JsonCleaner;
use ECSPrefix20210514\Symplify\ComposerJsonManipulator\Json\JsonInliner;
use ECSPrefix20210514\Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use ECSPrefix20210514\Symplify\PackageBuilder\Configuration\StaticEolConfiguration;
use ECSPrefix20210514\Symplify\SmartFileSystem\SmartFileInfo;
use ECSPrefix20210514\Symplify\SmartFileSystem\SmartFileSystem;
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
    public function __construct(\ECSPrefix20210514\Symplify\SmartFileSystem\SmartFileSystem $smartFileSystem, \ECSPrefix20210514\Symplify\ComposerJsonManipulator\Json\JsonCleaner $jsonCleaner, \ECSPrefix20210514\Symplify\ComposerJsonManipulator\Json\JsonInliner $jsonInliner)
    {
        $this->smartFileSystem = $smartFileSystem;
        $this->jsonCleaner = $jsonCleaner;
        $this->jsonInliner = $jsonInliner;
    }
    /**
     * @return mixed[]
     */
    public function loadFromFileInfo(\ECSPrefix20210514\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
    {
        $realPath = $smartFileInfo->getRealPath();
        if (!isset($this->cachedJSONFiles[$realPath])) {
            $this->cachedJSONFiles[$realPath] = \ECSPrefix20210514\Nette\Utils\Json::decode($smartFileInfo->getContents(), \ECSPrefix20210514\Nette\Utils\Json::FORCE_ARRAY);
        }
        return $this->cachedJSONFiles[$realPath];
    }
    /**
     * @return mixed[]
     * @param string $filePath
     */
    public function loadFromFilePath($filePath)
    {
        $filePath = (string) $filePath;
        $fileContent = $this->smartFileSystem->readFile($filePath);
        return \ECSPrefix20210514\Nette\Utils\Json::decode($fileContent, \ECSPrefix20210514\Nette\Utils\Json::FORCE_ARRAY);
    }
    /**
     * @param mixed[] $json
     * @return string
     */
    public function printJsonToFileInfo(array $json, \ECSPrefix20210514\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
    {
        $jsonString = $this->encodeJsonToFileContent($json);
        $this->smartFileSystem->dumpFile($smartFileInfo->getPathname(), $jsonString);
        return $jsonString;
    }
    /**
     * @param string $filePath
     * @return string
     */
    public function printComposerJsonToFilePath(\ECSPrefix20210514\Symplify\ComposerJsonManipulator\ValueObject\ComposerJson $composerJson, $filePath)
    {
        $filePath = (string) $filePath;
        $jsonString = $this->encodeJsonToFileContent($composerJson->getJsonArray());
        $this->smartFileSystem->dumpFile($filePath, $jsonString);
        return $jsonString;
    }
    /**
     * @param mixed[] $json
     * @return string
     */
    public function encodeJsonToFileContent(array $json)
    {
        // Empty arrays may lead to bad encoding since we can't be sure whether they need to be arrays or objects.
        $json = $this->jsonCleaner->removeEmptyKeysFromJsonArray($json);
        $jsonContent = \ECSPrefix20210514\Nette\Utils\Json::encode($json, \ECSPrefix20210514\Nette\Utils\Json::PRETTY) . \ECSPrefix20210514\Symplify\PackageBuilder\Configuration\StaticEolConfiguration::getEolChar();
        return $this->jsonInliner->inlineSections($jsonContent);
    }
}
