<?php

namespace Symplify\SmartFileSystem\Json;

use Nette\Utils\Arrays;
use Nette\Utils\Json;
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

    public function __construct(FileSystemGuard $fileSystemGuard, SmartFileSystem $smartFileSystem)
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
        $filePath = (string) $filePath;
        $this->fileSystemGuard->ensureFileExists($filePath, __METHOD__);

        $fileContent = $this->smartFileSystem->readFile($filePath);
        return Json::decode($fileContent, Json::FORCE_ARRAY);
    }

    /**
     * @param array<string, mixed> $jsonArray
     * @return void
     * @param string $filePath
     */
    public function writeJsonToFilePath(array $jsonArray, $filePath)
    {
        $filePath = (string) $filePath;
        $jsonContent = Json::encode($jsonArray, Json::PRETTY) . PHP_EOL;
        $this->smartFileSystem->dumpFile($filePath, $jsonContent);
    }

    /**
     * @param array<string, mixed> $newJsonArray
     * @return void
     * @param string $filePath
     */
    public function mergeArrayToJsonFile($filePath, array $newJsonArray)
    {
        $filePath = (string) $filePath;
        $jsonArray = $this->loadFilePathToJson($filePath);

        $newComposerJsonArray = Arrays::mergeTree($jsonArray, $newJsonArray);

        $this->writeJsonToFilePath($newComposerJsonArray, $filePath);
    }
}
