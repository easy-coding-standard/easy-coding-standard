<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\SmartFileSystem\Json;

use ConfigTransformer20210601\Nette\Utils\Arrays;
use ConfigTransformer20210601\Nette\Utils\Json;
use ConfigTransformer20210601\Symplify\SmartFileSystem\FileSystemGuard;
use ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileSystem;
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
    public function __construct(\ConfigTransformer20210601\Symplify\SmartFileSystem\FileSystemGuard $fileSystemGuard, \ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileSystem $smartFileSystem)
    {
        $this->fileSystemGuard = $fileSystemGuard;
        $this->smartFileSystem = $smartFileSystem;
    }
    /**
     * @return mixed[]
     */
    public function loadFilePathToJson(string $filePath) : array
    {
        $this->fileSystemGuard->ensureFileExists($filePath, __METHOD__);
        $fileContent = $this->smartFileSystem->readFile($filePath);
        return \ConfigTransformer20210601\Nette\Utils\Json::decode($fileContent, \ConfigTransformer20210601\Nette\Utils\Json::FORCE_ARRAY);
    }
    /**
     * @param array<string, mixed> $jsonArray
     * @return void
     */
    public function writeJsonToFilePath(array $jsonArray, string $filePath)
    {
        $jsonContent = \ConfigTransformer20210601\Nette\Utils\Json::encode($jsonArray, \ConfigTransformer20210601\Nette\Utils\Json::PRETTY) . \PHP_EOL;
        $this->smartFileSystem->dumpFile($filePath, $jsonContent);
    }
    /**
     * @param array<string, mixed> $newJsonArray
     * @return void
     */
    public function mergeArrayToJsonFile(string $filePath, array $newJsonArray)
    {
        $jsonArray = $this->loadFilePathToJson($filePath);
        $newComposerJsonArray = \ConfigTransformer20210601\Nette\Utils\Arrays::mergeTree($jsonArray, $newJsonArray);
        $this->writeJsonToFilePath($newComposerJsonArray, $filePath);
    }
}
