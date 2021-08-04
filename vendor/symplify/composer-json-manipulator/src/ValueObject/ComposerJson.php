<?php

declare (strict_types=1);
namespace ECSPrefix20210804\Symplify\ComposerJsonManipulator\ValueObject;

use ECSPrefix20210804\Nette\Utils\Arrays;
use ECSPrefix20210804\Nette\Utils\Strings;
use ECSPrefix20210804\Symplify\ComposerJsonManipulator\Sorter\ComposerPackageSorter;
use ECSPrefix20210804\Symplify\SmartFileSystem\SmartFileInfo;
use ECSPrefix20210804\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
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
     * @var string|null
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
     * @var \Symplify\SmartFileSystem\SmartFileInfo|null
     */
    private $fileInfo;
    /**
     * @var \Symplify\ComposerJsonManipulator\Sorter\ComposerPackageSorter
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
     * @var array<string, string>
     */
    private $suggest = [];
    /**
     * @var string|null
     */
    private $version;
    public function __construct()
    {
        $this->composerPackageSorter = new \ECSPrefix20210804\Symplify\ComposerJsonManipulator\Sorter\ComposerPackageSorter();
    }
    /**
     * @return void
     */
    public function setOriginalFileInfo(\ECSPrefix20210804\Symplify\SmartFileSystem\SmartFileInfo $fileInfo)
    {
        $this->fileInfo = $fileInfo;
    }
    /**
     * @return void
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }
    /**
     * @return void
     */
    public function setType(string $type)
    {
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
     */
    public function setVersion(string $version)
    {
        $this->version = $version;
    }
    /**
     * @return mixed[]
     */
    public function getRequire() : array
    {
        return $this->require;
    }
    /**
     * @return string|null
     */
    public function getRequirePhpVersion()
    {
        return $this->require[self::PHP] ?? null;
    }
    /**
     * @return array<string, string>
     */
    public function getRequirePhp() : array
    {
        $requiredPhpVersion = $this->require[self::PHP] ?? null;
        if ($requiredPhpVersion === null) {
            return [];
        }
        return [self::PHP => $requiredPhpVersion];
    }
    /**
     * @return mixed[]
     */
    public function getRequireDev() : array
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
     * @return string[]
     */
    public function getOrderedKeys() : array
    {
        return $this->orderedKeys;
    }
    /**
     * @return mixed[]
     */
    public function getAutoload() : array
    {
        return $this->autoload;
    }
    /**
     * @return string[]
     */
    public function getAbsoluteAutoloadDirectories() : array
    {
        if ($this->fileInfo === null) {
            throw new \ECSPrefix20210804\Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
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
    public function getAutoloadDev() : array
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
    public function getRepositories() : array
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
     */
    public function setMinimumStability(string $minimumStability)
    {
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
     */
    public function setPreferStable(bool $preferStable)
    {
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
    public function getExtra() : array
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
        return \ECSPrefix20210804\Nette\Utils\Strings::after($this->name, '/', -1);
    }
    /**
     * @return string[]
     */
    public function getReplace() : array
    {
        return $this->replace;
    }
    public function isReplacePackageSet(string $packageName) : bool
    {
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
     */
    public function setReplacePackage(string $packageName, string $version)
    {
        $this->replace[$packageName] = $version;
    }
    /**
     * @return mixed[]
     */
    public function getJsonArray() : array
    {
        $array = \array_filter([\ECSPrefix20210804\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::NAME => $this->name, \ECSPrefix20210804\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::DESCRIPTION => $this->description, \ECSPrefix20210804\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::KEYWORDS => $this->keywords, \ECSPrefix20210804\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::HOMEPAGE => $this->homepage, \ECSPrefix20210804\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::LICENSE => $this->license, \ECSPrefix20210804\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::AUTHORS => $this->authors, \ECSPrefix20210804\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::TYPE => $this->type, \ECSPrefix20210804\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::REQUIRE => $this->require, \ECSPrefix20210804\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::REQUIRE_DEV => $this->requireDev, \ECSPrefix20210804\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::AUTOLOAD => $this->autoload, \ECSPrefix20210804\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::AUTOLOAD_DEV => $this->autoloadDev, \ECSPrefix20210804\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::REPOSITORIES => $this->repositories, \ECSPrefix20210804\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::EXTRA => $this->extra, \ECSPrefix20210804\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::BIN => $this->bin, \ECSPrefix20210804\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::SCRIPTS => $this->scripts, \ECSPrefix20210804\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::SCRIPTS_DESCRIPTIONS => $this->scriptsDescriptions, \ECSPrefix20210804\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::SUGGEST => $this->suggest, \ECSPrefix20210804\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::CONFIG => $this->config, \ECSPrefix20210804\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::REPLACE => $this->replace, \ECSPrefix20210804\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::CONFLICT => $this->conflicts, \ECSPrefix20210804\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::VERSION => $this->version]);
        if ($this->minimumStability !== null) {
            $array[\ECSPrefix20210804\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::MINIMUM_STABILITY] = $this->minimumStability;
            $this->moveValueToBack(\ECSPrefix20210804\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::MINIMUM_STABILITY);
        }
        if ($this->preferStable !== null) {
            $array[\ECSPrefix20210804\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::PREFER_STABLE] = $this->preferStable;
            $this->moveValueToBack(\ECSPrefix20210804\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::PREFER_STABLE);
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
    public function getConfig() : array
    {
        return $this->config;
    }
    /**
     * @return void
     */
    public function setDescription(string $description)
    {
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
     * @return string[]
     */
    public function getKeywords() : array
    {
        return $this->keywords;
    }
    /**
     * @return void
     */
    public function setHomepage(string $homepage)
    {
        $this->homepage = $homepage;
    }
    /**
     * @return string|null
     */
    public function getHomepage()
    {
        return $this->homepage;
    }
    /**
     * @param string|string[]|null $license
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
    public function getAuthors() : array
    {
        return $this->authors;
    }
    public function hasPackage(string $packageName) : bool
    {
        if ($this->hasRequiredPackage($packageName)) {
            return \true;
        }
        return $this->hasRequiredDevPackage($packageName);
    }
    public function hasRequiredPackage(string $packageName) : bool
    {
        return isset($this->require[$packageName]);
    }
    public function hasRequiredDevPackage(string $packageName) : bool
    {
        return isset($this->requireDev[$packageName]);
    }
    /**
     * @return void
     */
    public function addRequiredPackage(string $packageName, string $version)
    {
        if (!$this->hasPackage($packageName)) {
            $this->require[$packageName] = $version;
            $this->require = $this->sortPackagesIfNeeded($this->require);
        }
    }
    /**
     * @return void
     */
    public function addRequiredDevPackage(string $packageName, string $version)
    {
        if (!$this->hasPackage($packageName)) {
            $this->requireDev[$packageName] = $version;
            $this->requireDev = $this->sortPackagesIfNeeded($this->requireDev);
        }
    }
    /**
     * @return void
     */
    public function changePackageVersion(string $packageName, string $version)
    {
        if ($this->hasRequiredPackage($packageName)) {
            $this->require[$packageName] = $version;
        }
        if ($this->hasRequiredDevPackage($packageName)) {
            $this->requireDev[$packageName] = $version;
        }
    }
    /**
     * @return void
     */
    public function movePackageToRequire(string $packageName)
    {
        if (!$this->hasRequiredDevPackage($packageName)) {
            return;
        }
        $version = $this->requireDev[$packageName];
        $this->removePackage($packageName);
        $this->addRequiredPackage($packageName, $version);
    }
    /**
     * @return void
     */
    public function movePackageToRequireDev(string $packageName)
    {
        if (!$this->hasRequiredPackage($packageName)) {
            return;
        }
        $version = $this->require[$packageName];
        $this->removePackage($packageName);
        $this->addRequiredDevPackage($packageName, $version);
    }
    /**
     * @return void
     */
    public function removePackage(string $packageName)
    {
        unset($this->require[$packageName], $this->requireDev[$packageName]);
    }
    /**
     * @return void
     */
    public function replacePackage(string $oldPackageName, string $newPackageName, string $targetVersion)
    {
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
    public function getBin() : array
    {
        return $this->bin;
    }
    /**
     * @return string[]
     */
    public function getPsr4AndClassmapDirectories() : array
    {
        $psr4Directories = \array_values($this->autoload['psr-4'] ?? []);
        $classmapDirectories = $this->autoload['classmap'] ?? [];
        return \array_merge($psr4Directories, $classmapDirectories);
    }
    /**
     * @return array<string, string|string[]>
     */
    public function getScripts() : array
    {
        return $this->scripts;
    }
    /**
     * @return array<string, string>
     */
    public function getScriptsDescriptions() : array
    {
        return $this->scriptsDescriptions;
    }
    /**
     * @return array<string, string>
     */
    public function getSuggest() : array
    {
        return $this->suggest;
    }
    /**
     * @return string[]
     */
    public function getAllClassmaps() : array
    {
        $autoloadClassmaps = $this->autoload[self::CLASSMAP_KEY] ?? [];
        $autoloadDevClassmaps = $this->autoloadDev[self::CLASSMAP_KEY] ?? [];
        return \array_merge($autoloadClassmaps, $autoloadDevClassmaps);
    }
    /**
     * @return array<string, string>
     */
    public function getConflicts() : array
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
     * @return string[]
     */
    public function getAutoloadDirectories() : array
    {
        $autoloadDirectories = \array_merge($this->getPsr4AndClassmapDirectories(), $this->getPsr4AndClassmapDevDirectories());
        return \ECSPrefix20210804\Nette\Utils\Arrays::flatten($autoloadDirectories);
    }
    /**
     * @return string[]
     */
    public function getPsr4AndClassmapDevDirectories() : array
    {
        $psr4Directories = \array_values($this->autoloadDev['psr-4'] ?? []);
        $classmapDirectories = $this->autoloadDev['classmap'] ?? [];
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
     * @param array<string, string> $suggest
     * @return void
     */
    public function setSuggest(array $suggest)
    {
        $this->suggest = $suggest;
    }
    /**
     * @return string[]
     */
    public function getDuplicatedRequirePackages() : array
    {
        $requiredPackageNames = $this->require;
        $requiredDevPackageNames = $this->requireDev;
        return \array_intersect($requiredPackageNames, $requiredDevPackageNames);
    }
    /**
     * @return string[]
     */
    public function getRequirePackageNames() : array
    {
        return \array_keys($this->require);
    }
    /**
     * @return void
     */
    private function moveValueToBack(string $valueName)
    {
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
    private function sortItemsByOrderedListOfKeys(array $contentItems, array $orderedVisibleItems) : array
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
            return $firstItemPosition <=> $secondItemPosition;
        });
        return $contentItems;
    }
    private function resolveExistingAutoloadDirectory(string $autoloadDirectory) : string
    {
        if ($this->fileInfo === null) {
            throw new \ECSPrefix20210804\Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
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
     * @return array<string, string>
     */
    private function sortPackagesIfNeeded(array $packages) : array
    {
        $sortPackages = $this->config['sort-packages'] ?? \false;
        if ($sortPackages) {
            return $this->composerPackageSorter->sortPackages($packages);
        }
        return $packages;
    }
    /**
     * @return int|string|bool
     */
    private function findPosition(string $key, array $items)
    {
        return \array_search($key, $items, \true);
    }
}
