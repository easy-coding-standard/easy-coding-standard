<?php

namespace ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject;

use ECSPrefix20210516\Nette\Utils\Arrays;
use ECSPrefix20210516\Nette\Utils\Strings;
use ECSPrefix20210516\Symplify\ComposerJsonManipulator\Sorter\ComposerPackageSorter;
use ECSPrefix20210516\Symplify\SmartFileSystem\SmartFileInfo;
use ECSPrefix20210516\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
/**
 * @api
 * @see \Symplify\ComposerJsonManipulator\Tests\ValueObject\ComposerJsonTest
 */
final class ComposerJson
{
    /**
     * @var string
     */
    const CLASSMAP_KEY = 'classmap';
    /**
     * @var string
     */
    const PHP = 'php';
    /**
     * @var string|null
     */
    private $name;
    /**
     * @var string|null
     */
    private $description;
    /**
     * @var string[]
     */
    private $keywords = [];
    /**
     * @var string
     */
    private $homepage;
    /**
     * @var string|string[]|null
     */
    private $license;
    /**
     * @var string|null
     */
    private $minimumStability;
    /**
     * @var bool|null
     */
    private $preferStable;
    /**
     * @var mixed[]
     */
    private $repositories = [];
    /**
     * @var array<string, mixed>
     */
    private $require = [];
    /**
     * @var mixed[]
     */
    private $autoload = [];
    /**
     * @var mixed[]
     */
    private $extra = [];
    /**
     * @var array<string, mixed>
     */
    private $requireDev = [];
    /**
     * @var mixed[]
     */
    private $autoloadDev = [];
    /**
     * @var string[]
     */
    private $orderedKeys = [];
    /**
     * @var string[]
     */
    private $replace = [];
    /**
     * @var array<string, string|string[]>
     */
    private $scripts = [];
    /**
     * @var mixed[]
     */
    private $config = [];
    /**
     * @var SmartFileInfo|null
     */
    private $fileInfo;
    /**
     * @var ComposerPackageSorter
     */
    private $composerPackageSorter;
    /**
     * @var array<string, string>
     */
    private $conflicts = [];
    /**
     * @var mixed[]
     */
    private $bin = [];
    /**
     * @var string|null
     */
    private $type;
    /**
     * @var mixed[]
     */
    private $authors = [];
    /**
     * @var array<string, string>
     */
    private $scriptsDescriptions = [];
    /**
     * @var string|null
     */
    private $version;
    public function __construct()
    {
        $this->composerPackageSorter = new \ECSPrefix20210516\Symplify\ComposerJsonManipulator\Sorter\ComposerPackageSorter();
    }
    /**
     * @return void
     */
    public function setOriginalFileInfo(\ECSPrefix20210516\Symplify\SmartFileSystem\SmartFileInfo $fileInfo)
    {
        $this->fileInfo = $fileInfo;
    }
    /**
     * @return void
     * @param string $name
     */
    public function setName($name)
    {
        $name = (string) $name;
        $this->name = $name;
    }
    /**
     * @return void
     * @param string $type
     */
    public function setType($type)
    {
        $type = (string) $type;
        $this->type = $type;
    }
    /**
     * @param mixed[] $require
     * @return void
     */
    public function setRequire(array $require)
    {
        $this->require = $this->sortPackagesIfNeeded($require);
    }
    /**
     * @return string|null
     */
    public function getVersion()
    {
        return $this->version;
    }
    /**
     * @return void
     * @param string $version
     */
    public function setVersion($version)
    {
        $version = (string) $version;
        $this->version = $version;
    }
    /**
     * @return mixed[]
     */
    public function getRequire()
    {
        return $this->require;
    }
    /**
     * @return string|null
     */
    public function getRequirePhpVersion()
    {
        return isset($this->require[self::PHP]) ? $this->require[self::PHP] : null;
    }
    /**
     * @return mixed[]
     */
    public function getRequirePhp()
    {
        $requiredPhpVersion = isset($this->require[self::PHP]) ? $this->require[self::PHP] : null;
        if ($requiredPhpVersion === null) {
            return [];
        }
        return [self::PHP => $requiredPhpVersion];
    }
    /**
     * @return mixed[]
     */
    public function getRequireDev()
    {
        return $this->requireDev;
    }
    /**
     * @return void
     */
    public function setRequireDev(array $requireDev)
    {
        $this->requireDev = $this->sortPackagesIfNeeded($requireDev);
    }
    /**
     * @param string[] $orderedKeys
     * @return void
     */
    public function setOrderedKeys(array $orderedKeys)
    {
        $this->orderedKeys = $orderedKeys;
    }
    /**
     * @return mixed[]
     */
    public function getOrderedKeys()
    {
        return $this->orderedKeys;
    }
    /**
     * @return mixed[]
     */
    public function getAutoload()
    {
        return $this->autoload;
    }
    /**
     * @return mixed[]
     */
    public function getAbsoluteAutoloadDirectories()
    {
        if ($this->fileInfo === null) {
            throw new \ECSPrefix20210516\Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
        }
        $autoloadDirectories = $this->getAutoloadDirectories();
        $absoluteAutoloadDirectories = [];
        foreach ($autoloadDirectories as $autoloadDirectory) {
            if (\is_file($autoloadDirectory)) {
                // skip files
                continue;
            }
            $absoluteAutoloadDirectories[] = $this->resolveExistingAutoloadDirectory($autoloadDirectory);
        }
        return $absoluteAutoloadDirectories;
    }
    /**
     * @param mixed[] $autoload
     * @return void
     */
    public function setAutoload(array $autoload)
    {
        $this->autoload = $autoload;
    }
    /**
     * @return mixed[]
     */
    public function getAutoloadDev()
    {
        return $this->autoloadDev;
    }
    /**
     * @param mixed[] $autoloadDev
     * @return void
     */
    public function setAutoloadDev(array $autoloadDev)
    {
        $this->autoloadDev = $autoloadDev;
    }
    /**
     * @return mixed[]
     */
    public function getRepositories()
    {
        return $this->repositories;
    }
    /**
     * @param mixed[] $repositories
     * @return void
     */
    public function setRepositories(array $repositories)
    {
        $this->repositories = $repositories;
    }
    /**
     * @return void
     * @param string $minimumStability
     */
    public function setMinimumStability($minimumStability)
    {
        $minimumStability = (string) $minimumStability;
        $this->minimumStability = $minimumStability;
    }
    /**
     * @return void
     */
    public function removeMinimumStability()
    {
        $this->minimumStability = null;
    }
    /**
     * @return string|null
     */
    public function getMinimumStability()
    {
        return $this->minimumStability;
    }
    /**
     * @return bool|null
     */
    public function getPreferStable()
    {
        return $this->preferStable;
    }
    /**
     * @return void
     * @param bool $preferStable
     */
    public function setPreferStable($preferStable)
    {
        $preferStable = (bool) $preferStable;
        $this->preferStable = $preferStable;
    }
    /**
     * @return void
     */
    public function removePreferStable()
    {
        $this->preferStable = null;
    }
    /**
     * @return mixed[]
     */
    public function getExtra()
    {
        return $this->extra;
    }
    /**
     * @param mixed[] $extra
     * @return void
     */
    public function setExtra(array $extra)
    {
        $this->extra = $extra;
    }
    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * @return string|null
     */
    public function getVendorName()
    {
        if ($this->name === null) {
            return null;
        }
        list($vendor) = \explode('/', $this->name);
        return $vendor;
    }
    /**
     * @return string|null
     */
    public function getShortName()
    {
        if ($this->name === null) {
            return null;
        }
        return \ECSPrefix20210516\Nette\Utils\Strings::after($this->name, '/', -1);
    }
    /**
     * @return mixed[]
     */
    public function getReplace()
    {
        return $this->replace;
    }
    /**
     * @param string $packageName
     * @return bool
     */
    public function isReplacePackageSet($packageName)
    {
        $packageName = (string) $packageName;
        return isset($this->replace[$packageName]);
    }
    /**
     * @param string[] $replace
     * @return void
     */
    public function setReplace(array $replace)
    {
        \ksort($replace);
        $this->replace = $replace;
    }
    /**
     * @return void
     * @param string $packageName
     * @param string $version
     */
    public function setReplacePackage($packageName, $version)
    {
        $packageName = (string) $packageName;
        $version = (string) $version;
        $this->replace[$packageName] = $version;
    }
    /**
     * @return mixed[]
     */
    public function getJsonArray()
    {
        $array = \array_filter([\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::NAME => $this->name, \ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::DESCRIPTION => $this->description, \ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::KEYWORDS => $this->keywords, \ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::HOMEPAGE => $this->homepage, \ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::LICENSE => $this->license, \ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::AUTHORS => $this->authors, \ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::TYPE => $this->type, \ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::REQUIRE => $this->require, \ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::REQUIRE_DEV => $this->requireDev, \ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::AUTOLOAD => $this->autoload, \ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::AUTOLOAD_DEV => $this->autoloadDev, \ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::REPOSITORIES => $this->repositories, \ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::EXTRA => $this->extra, \ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::BIN => $this->bin, \ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::SCRIPTS => $this->scripts, \ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::SCRIPTS_DESCRIPTIONS => $this->scriptsDescriptions, \ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::CONFIG => $this->config, \ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::REPLACE => $this->replace, \ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::CONFLICT => $this->conflicts, \ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::VERSION => $this->version]);
        if ($this->minimumStability !== null) {
            $array[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::MINIMUM_STABILITY] = $this->minimumStability;
            $this->moveValueToBack(\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::MINIMUM_STABILITY);
        }
        if ($this->preferStable !== null) {
            $array[\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::PREFER_STABLE] = $this->preferStable;
            $this->moveValueToBack(\ECSPrefix20210516\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::PREFER_STABLE);
        }
        return $this->sortItemsByOrderedListOfKeys($array, $this->orderedKeys);
    }
    /**
     * @param array<string, string|string[]> $scripts
     * @return void
     */
    public function setScripts(array $scripts)
    {
        $this->scripts = $scripts;
    }
    /**
     * @param mixed[] $config
     * @return void
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }
    /**
     * @return mixed[]
     */
    public function getConfig()
    {
        return $this->config;
    }
    /**
     * @return void
     * @param string $description
     */
    public function setDescription($description)
    {
        $description = (string) $description;
        $this->description = $description;
    }
    /**
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }
    /**
     * @param string[] $keywords
     * @return void
     */
    public function setKeywords(array $keywords)
    {
        $this->keywords = $keywords;
    }
    /**
     * @return mixed[]
     */
    public function getKeywords()
    {
        return $this->keywords;
    }
    /**
     * @return void
     * @param string $homepage
     */
    public function setHomepage($homepage)
    {
        $homepage = (string) $homepage;
        $this->homepage = $homepage;
    }
    /**
     * @return string
     */
    public function getHomepage()
    {
        return $this->homepage;
    }
    /**
     * @param string|array $license
     * @return void
     */
    public function setLicense($license)
    {
        $this->license = $license;
    }
    /**
     * @return string|string[]|null
     */
    public function getLicense()
    {
        return $this->license;
    }
    /**
     * @param mixed[] $authors
     * @return void
     */
    public function setAuthors(array $authors)
    {
        $this->authors = $authors;
    }
    /**
     * @return mixed[]
     */
    public function getAuthors()
    {
        return $this->authors;
    }
    /**
     * @param string $packageName
     * @return bool
     */
    public function hasPackage($packageName)
    {
        $packageName = (string) $packageName;
        if ($this->hasRequiredPackage($packageName)) {
            return \true;
        }
        return $this->hasRequiredDevPackage($packageName);
    }
    /**
     * @param string $packageName
     * @return bool
     */
    public function hasRequiredPackage($packageName)
    {
        $packageName = (string) $packageName;
        return isset($this->require[$packageName]);
    }
    /**
     * @param string $packageName
     * @return bool
     */
    public function hasRequiredDevPackage($packageName)
    {
        $packageName = (string) $packageName;
        return isset($this->requireDev[$packageName]);
    }
    /**
     * @return void
     * @param string $packageName
     * @param string $version
     */
    public function addRequiredPackage($packageName, $version)
    {
        $packageName = (string) $packageName;
        $version = (string) $version;
        if (!$this->hasPackage($packageName)) {
            $this->require[$packageName] = $version;
            $this->require = $this->sortPackagesIfNeeded($this->require);
        }
    }
    /**
     * @return void
     * @param string $packageName
     * @param string $version
     */
    public function addRequiredDevPackage($packageName, $version)
    {
        $packageName = (string) $packageName;
        $version = (string) $version;
        if (!$this->hasPackage($packageName)) {
            $this->requireDev[$packageName] = $version;
            $this->requireDev = $this->sortPackagesIfNeeded($this->requireDev);
        }
    }
    /**
     * @return void
     * @param string $packageName
     * @param string $version
     */
    public function changePackageVersion($packageName, $version)
    {
        $packageName = (string) $packageName;
        $version = (string) $version;
        if ($this->hasRequiredPackage($packageName)) {
            $this->require[$packageName] = $version;
        }
        if ($this->hasRequiredDevPackage($packageName)) {
            $this->requireDev[$packageName] = $version;
        }
    }
    /**
     * @return void
     * @param string $packageName
     */
    public function movePackageToRequire($packageName)
    {
        $packageName = (string) $packageName;
        if (!$this->hasRequiredDevPackage($packageName)) {
            return;
        }
        $version = $this->requireDev[$packageName];
        $this->removePackage($packageName);
        $this->addRequiredPackage($packageName, $version);
    }
    /**
     * @return void
     * @param string $packageName
     */
    public function movePackageToRequireDev($packageName)
    {
        $packageName = (string) $packageName;
        if (!$this->hasRequiredPackage($packageName)) {
            return;
        }
        $version = $this->require[$packageName];
        $this->removePackage($packageName);
        $this->addRequiredDevPackage($packageName, $version);
    }
    /**
     * @return void
     * @param string $packageName
     */
    public function removePackage($packageName)
    {
        $packageName = (string) $packageName;
        unset($this->require[$packageName], $this->requireDev[$packageName]);
    }
    /**
     * @return void
     * @param string $oldPackageName
     * @param string $newPackageName
     * @param string $targetVersion
     */
    public function replacePackage($oldPackageName, $newPackageName, $targetVersion)
    {
        $oldPackageName = (string) $oldPackageName;
        $newPackageName = (string) $newPackageName;
        $targetVersion = (string) $targetVersion;
        if ($this->hasRequiredPackage($oldPackageName)) {
            unset($this->require[$oldPackageName]);
            $this->addRequiredPackage($newPackageName, $targetVersion);
        }
        if ($this->hasRequiredDevPackage($oldPackageName)) {
            unset($this->requireDev[$oldPackageName]);
            $this->addRequiredDevPackage($newPackageName, $targetVersion);
        }
    }
    /**
     * @return \Symplify\SmartFileSystem\SmartFileInfo|null
     */
    public function getFileInfo()
    {
        return $this->fileInfo;
    }
    /**
     * @param array<string, string> $conflicts
     * @return void
     */
    public function setConflicts(array $conflicts)
    {
        $this->conflicts = $conflicts;
    }
    /**
     * @param mixed[] $bin
     * @return void
     */
    public function setBin(array $bin)
    {
        $this->bin = $bin;
    }
    /**
     * @return mixed[]
     */
    public function getBin()
    {
        return $this->bin;
    }
    /**
     * @return mixed[]
     */
    public function getPsr4AndClassmapDirectories()
    {
        $psr4Directories = \array_values(isset($this->autoload['psr-4']) ? $this->autoload['psr-4'] : []);
        $classmapDirectories = isset($this->autoload['classmap']) ? $this->autoload['classmap'] : [];
        return \array_merge($psr4Directories, $classmapDirectories);
    }
    /**
     * @return mixed[]
     */
    public function getScripts()
    {
        return $this->scripts;
    }
    /**
     * @return mixed[]
     */
    public function getScriptsDescriptions()
    {
        return $this->scriptsDescriptions;
    }
    /**
     * @return mixed[]
     */
    public function getAllClassmaps()
    {
        $autoloadClassmaps = isset($this->autoload[self::CLASSMAP_KEY]) ? $this->autoload[self::CLASSMAP_KEY] : [];
        $autoloadDevClassmaps = isset($this->autoloadDev[self::CLASSMAP_KEY]) ? $this->autoloadDev[self::CLASSMAP_KEY] : [];
        return \array_merge($autoloadClassmaps, $autoloadDevClassmaps);
    }
    /**
     * @return mixed[]
     */
    public function getConflicts()
    {
        return $this->conflicts;
    }
    /**
     * @api
     * @return string|null
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * @return mixed[]
     */
    public function getAutoloadDirectories()
    {
        $autoloadDirectories = \array_merge($this->getPsr4AndClassmapDirectories(), $this->getPsr4AndClassmapDevDirectories());
        return \ECSPrefix20210516\Nette\Utils\Arrays::flatten($autoloadDirectories);
    }
    /**
     * @return mixed[]
     */
    public function getPsr4AndClassmapDevDirectories()
    {
        $psr4Directories = \array_values(isset($this->autoloadDev['psr-4']) ? $this->autoloadDev['psr-4'] : []);
        $classmapDirectories = isset($this->autoloadDev['classmap']) ? $this->autoloadDev['classmap'] : [];
        return \array_merge($psr4Directories, $classmapDirectories);
    }
    /**
     * @param array<string, string> $scriptsDescriptions
     * @return void
     */
    public function setScriptsDescriptions(array $scriptsDescriptions)
    {
        $this->scriptsDescriptions = $scriptsDescriptions;
    }
    /**
     * @return mixed[]
     */
    public function getDuplicatedRequirePackages()
    {
        $requiredPackageNames = $this->require;
        $requiredDevPackageNames = $this->requireDev;
        return \array_intersect($requiredPackageNames, $requiredDevPackageNames);
    }
    /**
     * @return mixed[]
     */
    public function getRequirePackageNames()
    {
        return \array_keys($this->require);
    }
    /**
     * @return void
     * @param string $valueName
     */
    private function moveValueToBack($valueName)
    {
        $valueName = (string) $valueName;
        $key = \array_search($valueName, $this->orderedKeys, \true);
        if ($key !== \false) {
            unset($this->orderedKeys[$key]);
        }
        $this->orderedKeys[] = $valueName;
    }
    /**
     * 2. sort item by prescribed key order
     *
     * @see https://www.designcise.com/web/tutorial/how-to-sort-an-array-by-keys-based-on-order-in-a-secondary-array-in-php
     * @param array<string, mixed> $contentItems
     * @param string[] $orderedVisibleItems
     * @return mixed[]
     */
    private function sortItemsByOrderedListOfKeys(array $contentItems, array $orderedVisibleItems)
    {
        \uksort($contentItems, function ($firstContentItem, $secondContentItem) use($orderedVisibleItems) : int {
            $firstItemPosition = $this->findPosition($firstContentItem, $orderedVisibleItems);
            $secondItemPosition = $this->findPosition($secondContentItem, $orderedVisibleItems);
            if ($firstItemPosition === \false) {
                // new item, put in the back
                return -1;
            }
            if ($secondItemPosition === \false) {
                // new item, put in the back
                return -1;
            }
            $battleShipcompare = function ($left, $right) {
                if ($left === $right) {
                    return 0;
                }
                return $left < $right ? -1 : 1;
            };
            return $battleShipcompare($firstItemPosition, $secondItemPosition);
        });
        return $contentItems;
    }
    /**
     * @param string $autoloadDirectory
     * @return string
     */
    private function resolveExistingAutoloadDirectory($autoloadDirectory)
    {
        $autoloadDirectory = (string) $autoloadDirectory;
        if ($this->fileInfo === null) {
            throw new \ECSPrefix20210516\Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
        }
        $filePathCandidates = [
            $this->fileInfo->getPath() . \DIRECTORY_SEPARATOR . $autoloadDirectory,
            // mostly tests
            \getcwd() . \DIRECTORY_SEPARATOR . $autoloadDirectory,
        ];
        foreach ($filePathCandidates as $filePathCandidate) {
            if (\file_exists($filePathCandidate)) {
                return $filePathCandidate;
            }
        }
        return $autoloadDirectory;
    }
    /**
     * @return mixed[]
     */
    private function sortPackagesIfNeeded(array $packages)
    {
        $sortPackages = isset($this->config['sort-packages']) ? $this->config['sort-packages'] : \false;
        if ($sortPackages) {
            return $this->composerPackageSorter->sortPackages($packages);
        }
        return $packages;
    }
    /**
     * @return int|string|bool
     * @param string $key
     */
    private function findPosition($key, array $items)
    {
        $key = (string) $key;
        return \array_search($key, $items, \true);
    }
}
