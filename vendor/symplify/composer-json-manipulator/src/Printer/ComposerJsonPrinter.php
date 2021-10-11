<?php

declare (strict_types=1);
namespace ECSPrefix20211011\Symplify\ComposerJsonManipulator\Printer;

use ECSPrefix20211011\Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;
use ECSPrefix20211011\Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use ECSPrefix20211011\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @api
 */
final class ComposerJsonPrinter
{
    /**
     * @var \Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager
     */
    private $jsonFileManager;
    public function __construct(\ECSPrefix20211011\Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager $jsonFileManager)
    {
        $this->jsonFileManager = $jsonFileManager;
    }
    /**
     * @param string|\Symplify\SmartFileSystem\SmartFileInfo $targetFile
     */
    public function print(\ECSPrefix20211011\Symplify\ComposerJsonManipulator\ValueObject\ComposerJson $composerJson, $targetFile) : string
    {
        if (\is_string($targetFile)) {
            return $this->jsonFileManager->printComposerJsonToFilePath($composerJson, $targetFile);
        }
        return $this->jsonFileManager->printJsonToFileInfo($composerJson->getJsonArray(), $targetFile);
    }
}
