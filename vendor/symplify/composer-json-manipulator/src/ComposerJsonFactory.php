<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\ComposerJsonManipulator;

use ConfigTransformer20210601\Nette\Utils\Json;
use ConfigTransformer20210601\Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;
use ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection;
use ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @see \Symplify\ComposerJsonManipulator\Tests\ComposerJsonFactory\ComposerJsonFactoryTest
 */
final class ComposerJsonFactory
{
    /**
     * @var JsonFileManager
     */
    private $jsonFileManager;
    public function __construct(\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager $jsonFileManager)
    {
        $this->jsonFileManager = $jsonFileManager;
    }
    public function createFromString(string $jsonString) : \ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJson
    {
        $jsonArray = \ConfigTransformer20210601\Nette\Utils\Json::decode($jsonString, \ConfigTransformer20210601\Nette\Utils\Json::FORCE_ARRAY);
        return $this->createFromArray($jsonArray);
    }
    public function createFromFileInfo(\ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo) : \ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJson
    {
        $jsonArray = $this->jsonFileManager->loadFromFilePath($smartFileInfo->getRealPath());
        $composerJson = $this->createFromArray($jsonArray);
        $composerJson->setOriginalFileInfo($smartFileInfo);
        return $composerJson;
    }
    public function createFromFilePath(string $filePath) : \ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJson
    {
        $jsonArray = $this->jsonFileManager->loadFromFilePath($filePath);
        $composerJson = $this->createFromArray($jsonArray);
        $fileInfo = new \ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileInfo($filePath);
        $composerJson->setOriginalFileInfo($fileInfo);
        return $composerJson;
    }
    public function createEmpty() : \ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJson
    {
        return new \ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJson();
    }
    /**
     * @param mixed[] $jsonArray
     */
    public function createFromArray(array $jsonArray) : \ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJson
    {
        $composerJson = new \ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJson();
        if (isset($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::CONFIG])) {
            $composerJson->setConfig($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::CONFIG]);
        }
        if (isset($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::NAME])) {
            $composerJson->setName($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::NAME]);
        }
        if (isset($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::TYPE])) {
            $composerJson->setType($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::TYPE]);
        }
        if (isset($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::AUTHORS])) {
            $composerJson->setAuthors($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::AUTHORS]);
        }
        if (isset($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::DESCRIPTION])) {
            $composerJson->setDescription($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::DESCRIPTION]);
        }
        if (isset($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::KEYWORDS])) {
            $composerJson->setKeywords($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::KEYWORDS]);
        }
        if (isset($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::HOMEPAGE])) {
            $composerJson->setHomepage($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::HOMEPAGE]);
        }
        if (isset($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::LICENSE])) {
            $composerJson->setLicense($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::LICENSE]);
        }
        if (isset($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::BIN])) {
            $composerJson->setBin($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::BIN]);
        }
        if (isset($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::REQUIRE])) {
            $composerJson->setRequire($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::REQUIRE]);
        }
        if (isset($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::REQUIRE_DEV])) {
            $composerJson->setRequireDev($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::REQUIRE_DEV]);
        }
        if (isset($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::AUTOLOAD])) {
            $composerJson->setAutoload($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::AUTOLOAD]);
        }
        if (isset($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::AUTOLOAD_DEV])) {
            $composerJson->setAutoloadDev($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::AUTOLOAD_DEV]);
        }
        if (isset($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::REPLACE])) {
            $composerJson->setReplace($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::REPLACE]);
        }
        if (isset($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::EXTRA])) {
            $composerJson->setExtra($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::EXTRA]);
        }
        if (isset($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::SCRIPTS])) {
            $composerJson->setScripts($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::SCRIPTS]);
        }
        if (isset($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::SCRIPTS_DESCRIPTIONS])) {
            $composerJson->setScriptsDescriptions($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::SCRIPTS_DESCRIPTIONS]);
        }
        if (isset($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::MINIMUM_STABILITY])) {
            $composerJson->setMinimumStability($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::MINIMUM_STABILITY]);
        }
        if (isset($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::PREFER_STABLE])) {
            $composerJson->setPreferStable($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::PREFER_STABLE]);
        }
        if (isset($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::CONFLICT])) {
            $composerJson->setConflicts($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::CONFLICT]);
        }
        if (isset($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::REPOSITORIES])) {
            $composerJson->setRepositories($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::REPOSITORIES]);
        }
        if (isset($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::VERSION])) {
            $composerJson->setVersion($jsonArray[\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::VERSION]);
        }
        $orderedKeys = \array_keys($jsonArray);
        $composerJson->setOrderedKeys($orderedKeys);
        return $composerJson;
    }
}
