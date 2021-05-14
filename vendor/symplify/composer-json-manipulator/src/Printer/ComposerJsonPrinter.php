<?php

namespace ECSPrefix20210514\Symplify\ComposerJsonManipulator\Printer;

use ECSPrefix20210514\Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;
use ECSPrefix20210514\Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use ECSPrefix20210514\Symplify\SmartFileSystem\SmartFileInfo;
final class ComposerJsonPrinter
{
    /**
     * @var JsonFileManager
     */
    private $jsonFileManager;
    public function __construct(\ECSPrefix20210514\Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager $jsonFileManager)
    {
        $this->jsonFileManager = $jsonFileManager;
    }
    /**
     * @return string
     */
    public function printToString(\ECSPrefix20210514\Symplify\ComposerJsonManipulator\ValueObject\ComposerJson $composerJson)
    {
        return $this->jsonFileManager->encodeJsonToFileContent($composerJson->getJsonArray());
    }
    /**
     * @param string|SmartFileInfo $targetFile
     * @return string
     */
    public function print(\ECSPrefix20210514\Symplify\ComposerJsonManipulator\ValueObject\ComposerJson $composerJson, $targetFile)
    {
        if (\is_string($targetFile)) {
            return $this->jsonFileManager->printComposerJsonToFilePath($composerJson, $targetFile);
        }
        return $this->jsonFileManager->printJsonToFileInfo($composerJson->getJsonArray(), $targetFile);
    }
}
