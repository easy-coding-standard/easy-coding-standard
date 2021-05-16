<?php

namespace ECSPrefix20210516\Symplify\ComposerJsonManipulator;

use ECSPrefix20210516\Nette\Utils\Json;
use ECSPrefix20210516\Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;
use ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection;
use ECSPrefix20210516\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @see \Symplify\ComposerJsonManipulator\Tests\ComposerJsonFactory\ComposerJsonFactoryTest
 */
final class ComposerJsonFactory
{
    /**
     * @var JsonFileManager
     */
    private $jsonFileManager;
    public function __construct(\ECSPrefix20210516\Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager $jsonFileManager)
    {
        $this->jsonFileManager = $jsonFileManager;
    }
    /**
     * @param string $jsonString
     * @return \Symplify\ComposerJsonManipulator\ValueObject\ComposerJson
     */
    public function createFromString($jsonString)
    {
        $jsonString = (string) $jsonString;
        $jsonArray = \ECSPrefix20210516\Nette\Utils\Json::decode($jsonString, \ECSPrefix20210516\Nette\Utils\Json::FORCE_ARRAY);
        return $this->createFromArray($jsonArray);
    }
    /**
     * @return \Symplify\ComposerJsonManipulator\ValueObject\ComposerJson
     */
    public function createFromFileInfo(\ECSPrefix20210516\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
    {
        $jsonArray = $this->jsonFileManager->loadFromFilePath($smartFileInfo->getRealPath());
        $composerJson = $this->createFromArray($jsonArray);
        $composerJson->setOriginalFileInfo($smartFileInfo);
        return $composerJson;
    }
    /**
     * @param string $filePath
     * @return \Symplify\ComposerJsonManipulator\ValueObject\ComposerJson
     */
    public function createFromFilePath($filePath)
    {
        $filePath = (string) $filePath;
        $jsonArray = $this->jsonFileManager->loadFromFilePath($filePath);
        $composerJson = $this->createFromArray($jsonArray);
        $fileInfo = new \ECSPrefix20210516\Symplify\SmartFileSystem\SmartFileInfo($filePath);
        $composerJson->setOriginalFileInfo($fileInfo);
        return $composerJson;
    }
    /**
     * @return \Symplify\ComposerJsonManipulator\ValueObject\ComposerJson
     */
    public function createEmpty()
    {
        return new \ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJson();
    }
    /**
     * @param mixed[] $jsonArray
     * @return \Symplify\ComposerJsonManipulator\ValueObject\ComposerJson
     */
    public function createFromArray(array $jsonArray)
    {
        $composerJson = new \ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJson();
        if (isset($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::CONFIG])) {
            $composerJson->setConfig($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::CONFIG]);
        }
        if (isset($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::NAME])) {
            $composerJson->setName($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::NAME]);
        }
        if (isset($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::TYPE])) {
            $composerJson->setType($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::TYPE]);
        }
        if (isset($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::AUTHORS])) {
            $composerJson->setAuthors($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::AUTHORS]);
        }
        if (isset($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::DESCRIPTION])) {
            $composerJson->setDescription($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::DESCRIPTION]);
        }
        if (isset($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::KEYWORDS])) {
            $composerJson->setKeywords($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::KEYWORDS]);
        }
        if (isset($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::HOMEPAGE])) {
            $composerJson->setHomepage($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::HOMEPAGE]);
        }
        if (isset($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::LICENSE])) {
            $composerJson->setLicense($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::LICENSE]);
        }
        if (isset($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::BIN])) {
            $composerJson->setBin($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::BIN]);
        }
        if (isset($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::REQUIRE])) {
            $composerJson->setRequire($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::REQUIRE]);
        }
        if (isset($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::REQUIRE_DEV])) {
            $composerJson->setRequireDev($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::REQUIRE_DEV]);
        }
        if (isset($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::AUTOLOAD])) {
            $composerJson->setAutoload($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::AUTOLOAD]);
        }
        if (isset($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::AUTOLOAD_DEV])) {
            $composerJson->setAutoloadDev($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::AUTOLOAD_DEV]);
        }
        if (isset($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::REPLACE])) {
            $composerJson->setReplace($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::REPLACE]);
        }
        if (isset($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::EXTRA])) {
            $composerJson->setExtra($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::EXTRA]);
        }
        if (isset($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::SCRIPTS])) {
            $composerJson->setScripts($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::SCRIPTS]);
        }
        if (isset($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::SCRIPTS_DESCRIPTIONS])) {
            $composerJson->setScriptsDescriptions($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::SCRIPTS_DESCRIPTIONS]);
        }
        if (isset($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::MINIMUM_STABILITY])) {
            $composerJson->setMinimumStability($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::MINIMUM_STABILITY]);
        }
        if (isset($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::PREFER_STABLE])) {
            $composerJson->setPreferStable($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::PREFER_STABLE]);
        }
        if (isset($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::CONFLICT])) {
            $composerJson->setConflicts($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::CONFLICT]);
        }
        if (isset($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::REPOSITORIES])) {
            $composerJson->setRepositories($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::REPOSITORIES]);
        }
        if (isset($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::VERSION])) {
            $composerJson->setVersion($jsonArray[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::VERSION]);
        }
        $orderedKeys = \array_keys($jsonArray);
        $composerJson->setOrderedKeys($orderedKeys);
        return $composerJson;
    }
}
