<?php

declare (strict_types=1);
namespace ECSPrefix20220220\Symplify\ComposerJsonManipulator;

use ECSPrefix20220220\Nette\Utils\Json;
use ECSPrefix20220220\Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;
use ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection;
use ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @api
 * @see \Symplify\ComposerJsonManipulator\Tests\ComposerJsonFactory\ComposerJsonFactoryTest
 */
final class ComposerJsonFactory
{
    /**
     * @var \Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager
     */
    private $jsonFileManager;
    public function __construct(\ECSPrefix20220220\Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager $jsonFileManager)
    {
        $this->jsonFileManager = $jsonFileManager;
    }
    public function createFromString(string $jsonString) : \ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJson
    {
        $jsonArray = \ECSPrefix20220220\Nette\Utils\Json::decode($jsonString, \ECSPrefix20220220\Nette\Utils\Json::FORCE_ARRAY);
        return $this->createFromArray($jsonArray);
    }
    public function createFromFileInfo(\ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo) : \ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJson
    {
        $jsonArray = $this->jsonFileManager->loadFromFilePath($smartFileInfo->getRealPath());
        $composerJson = $this->createFromArray($jsonArray);
        $composerJson->setOriginalFileInfo($smartFileInfo);
        return $composerJson;
    }
    public function createFromFilePath(string $filePath) : \ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJson
    {
        $jsonArray = $this->jsonFileManager->loadFromFilePath($filePath);
        $composerJson = $this->createFromArray($jsonArray);
        $fileInfo = new \ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo($filePath);
        $composerJson->setOriginalFileInfo($fileInfo);
        return $composerJson;
    }
    public function createEmpty() : \ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJson
    {
        return new \ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJson();
    }
    /**
     * @param mixed[] $jsonArray
     */
    public function createFromArray(array $jsonArray) : \ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJson
    {
        $composerJson = new \ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJson();
        if (isset($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::CONFIG])) {
            $composerJson->setConfig($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::CONFIG]);
        }
        if (isset($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::NAME])) {
            $composerJson->setName($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::NAME]);
        }
        if (isset($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::TYPE])) {
            $composerJson->setType($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::TYPE]);
        }
        if (isset($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::AUTHORS])) {
            $composerJson->setAuthors($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::AUTHORS]);
        }
        if (isset($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::DESCRIPTION])) {
            $composerJson->setDescription($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::DESCRIPTION]);
        }
        if (isset($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::KEYWORDS])) {
            $composerJson->setKeywords($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::KEYWORDS]);
        }
        if (isset($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::HOMEPAGE])) {
            $composerJson->setHomepage($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::HOMEPAGE]);
        }
        if (isset($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::LICENSE])) {
            $composerJson->setLicense($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::LICENSE]);
        }
        if (isset($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::BIN])) {
            $composerJson->setBin($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::BIN]);
        }
        if (isset($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::REQUIRE])) {
            $composerJson->setRequire($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::REQUIRE]);
        }
        if (isset($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::REQUIRE_DEV])) {
            $composerJson->setRequireDev($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::REQUIRE_DEV]);
        }
        if (isset($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::AUTOLOAD])) {
            $composerJson->setAutoload($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::AUTOLOAD]);
        }
        if (isset($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::AUTOLOAD_DEV])) {
            $composerJson->setAutoloadDev($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::AUTOLOAD_DEV]);
        }
        if (isset($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::REPLACE])) {
            $composerJson->setReplace($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::REPLACE]);
        }
        if (isset($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::EXTRA])) {
            $composerJson->setExtra($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::EXTRA]);
        }
        if (isset($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::SCRIPTS])) {
            $composerJson->setScripts($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::SCRIPTS]);
        }
        if (isset($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::SCRIPTS_DESCRIPTIONS])) {
            $composerJson->setScriptsDescriptions($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::SCRIPTS_DESCRIPTIONS]);
        }
        if (isset($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::SUGGEST])) {
            $composerJson->setSuggest($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::SUGGEST]);
        }
        if (isset($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::MINIMUM_STABILITY])) {
            $composerJson->setMinimumStability($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::MINIMUM_STABILITY]);
        }
        if (isset($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::PREFER_STABLE])) {
            $composerJson->setPreferStable($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::PREFER_STABLE]);
        }
        if (isset($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::CONFLICT])) {
            $composerJson->setConflicts($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::CONFLICT]);
        }
        if (isset($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::REPOSITORIES])) {
            $composerJson->setRepositories($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::REPOSITORIES]);
        }
        if (isset($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::VERSION])) {
            $composerJson->setVersion($jsonArray[\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::VERSION]);
        }
        $orderedKeys = \array_keys($jsonArray);
        $composerJson->setOrderedKeys($orderedKeys);
        return $composerJson;
    }
}
