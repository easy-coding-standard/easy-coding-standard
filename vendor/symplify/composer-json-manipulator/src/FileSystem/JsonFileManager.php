<?php

namespace Symplify\ComposerJsonManipulator\FileSystem;

use ECSPrefix20210513\Nette\Utils\Json;
use Symplify\ComposerJsonManipulator\Json\JsonCleaner;
use Symplify\ComposerJsonManipulator\Json\JsonInliner;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\PackageBuilder\Configuration\StaticEolConfiguration;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;
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
    public function __construct(\Symplify\SmartFileSystem\SmartFileSystem $smartFileSystem, \Symplify\ComposerJsonManipulator\Json\JsonCleaner $jsonCleaner, \Symplify\ComposerJsonManipulator\Json\JsonInliner $jsonInliner)
    {
        $this->smartFileSystem = $smartFileSystem;
        $this->jsonCleaner = $jsonCleaner;
        $this->jsonInliner = $jsonInliner;
    }
    /**
     * @return mixed[]
     */
    public function loadFromFileInfo(\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
    {
        $realPath = $smartFileInfo->getRealPath();
        if (!isset($this->cachedJSONFiles[$realPath])) {
            $this->cachedJSONFiles[$realPath] = \ECSPrefix20210513\Nette\Utils\Json::decode($smartFileInfo->getContents(), \ECSPrefix20210513\Nette\Utils\Json::FORCE_ARRAY);
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
        return \ECSPrefix20210513\Nette\Utils\Json::decode($fileContent, \ECSPrefix20210513\Nette\Utils\Json::FORCE_ARRAY);
    }
    /**
     * @param mixed[] $json
     * @return string
     */
    public function printJsonToFileInfo(array $json, \Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
    {
        $jsonString = $this->encodeJsonToFileContent($json);
        $this->smartFileSystem->dumpFile($smartFileInfo->getPathname(), $jsonString);
        return $jsonString;
    }
    /**
     * @param string $filePath
     * @return string
     */
    public function printComposerJsonToFilePath(\Symplify\ComposerJsonManipulator\ValueObject\ComposerJson $composerJson, $filePath)
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
        $jsonContent = \ECSPrefix20210513\Nette\Utils\Json::encode($json, \ECSPrefix20210513\Nette\Utils\Json::PRETTY) . \Symplify\PackageBuilder\Configuration\StaticEolConfiguration::getEolChar();
        return $this->jsonInliner->inlineSections($jsonContent);
    }
}
