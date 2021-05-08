<?php

namespace Symplify\SmartFileSystem\Json;

use ECSPrefix20210508\Nette\Utils\Arrays;
use ECSPrefix20210508\Nette\Utils\Json;
use Symplify\SmartFileSystem\FileSystemGuard;
use Symplify\SmartFileSystem\SmartFileSystem;
/**
 * @see \Symplify\SmartFileSystem\Tests\Json\JsonFileSystem\JsonFileSystemTest
 */
final class JsonFileSystem
{
    /**
     * @var FileSystemGuard
     */
    private $fileSystemGuard;
    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;
    public function __construct(\Symplify\SmartFileSystem\FileSystemGuard $fileSystemGuard, \Symplify\SmartFileSystem\SmartFileSystem $smartFileSystem)
    {
        $this->fileSystemGuard = $fileSystemGuard;
        $this->smartFileSystem = $smartFileSystem;
    }
    /**
     * @return mixed[]
     * @param string $filePath
     */
    public function loadFilePathToJson($filePath)
    {
        $this->fileSystemGuard->ensureFileExists($filePath, __METHOD__);
        $fileContent = $this->smartFileSystem->readFile($filePath);
        return \ECSPrefix20210508\Nette\Utils\Json::decode($fileContent, \ECSPrefix20210508\Nette\Utils\Json::FORCE_ARRAY);
    }
    /**
     * @param array<string, mixed> $jsonArray
     * @return void
     * @param string $filePath
     */
    public function writeJsonToFilePath(array $jsonArray, $filePath)
    {
        $jsonContent = \ECSPrefix20210508\Nette\Utils\Json::encode($jsonArray, \ECSPrefix20210508\Nette\Utils\Json::PRETTY) . \PHP_EOL;
        $this->smartFileSystem->dumpFile($filePath, $jsonContent);
    }
    /**
     * @param array<string, mixed> $newJsonArray
     * @return void
     * @param string $filePath
     */
    public function mergeArrayToJsonFile($filePath, array $newJsonArray)
    {
        $jsonArray = $this->loadFilePathToJson($filePath);
        $newComposerJsonArray = \ECSPrefix20210508\Nette\Utils\Arrays::mergeTree($jsonArray, $newJsonArray);
        $this->writeJsonToFilePath($newComposerJsonArray, $filePath);
    }
}
