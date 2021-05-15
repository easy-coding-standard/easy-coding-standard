<?php

namespace ECSPrefix20210515\Symplify\ComposerJsonManipulator\Printer;

use ECSPrefix20210515\Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;
use ECSPrefix20210515\Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use ECSPrefix20210515\Symplify\SmartFileSystem\SmartFileInfo;
final class ComposerJsonPrinter
{
    /**
     * @var JsonFileManager
     */
    private $jsonFileManager;
    public function __construct(\ECSPrefix20210515\Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager $jsonFileManager)
    {
        $this->jsonFileManager = $jsonFileManager;
    }
    /**
     * @return string
     */
    public function printToString(\ECSPrefix20210515\Symplify\ComposerJsonManipulator\ValueObject\ComposerJson $composerJson)
    {
        return $this->jsonFileManager->encodeJsonToFileContent($composerJson->getJsonArray());
    }
    /**
     * @param string|SmartFileInfo $targetFile
     * @return string
     */
    public function print(\ECSPrefix20210515\Symplify\ComposerJsonManipulator\ValueObject\ComposerJson $composerJson, $targetFile)
    {
        if (\is_string($targetFile)) {
            return $this->jsonFileManager->printComposerJsonToFilePath($composerJson, $targetFile);
        }
        return $this->jsonFileManager->printJsonToFileInfo($composerJson->getJsonArray(), $targetFile);
    }
}
