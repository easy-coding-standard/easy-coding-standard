<?php

namespace Symplify\SmartFileSystem;

use ECSPrefix20210511\Nette\Utils\Strings;
use ECSPrefix20210511\Symfony\Component\Finder\SplFileInfo;
use Symplify\EasyTesting\PHPUnit\StaticPHPUnitEnvironment;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\SmartFileSystem\Exception\DirectoryNotFoundException;
use Symplify\SmartFileSystem\Exception\FileNotFoundException;
/**
 * @see \Symplify\SmartFileSystem\Tests\SmartFileInfo\SmartFileInfoTest
 */
final class SmartFileInfo extends \ECSPrefix20210511\Symfony\Component\Finder\SplFileInfo
{
    /**
     * @var string
     * @see https://regex101.com/r/SYP00O/1
     */
    const LAST_SUFFIX_REGEX = '#\\.[^.]+$#';
    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;
    /**
     * @param string $filePath
     */
    public function __construct($filePath)
    {
        $filePath = (string) $filePath;
        $this->smartFileSystem = new \Symplify\SmartFileSystem\SmartFileSystem();
        // accepts also dirs
        if (!\file_exists($filePath)) {
            throw new \Symplify\SmartFileSystem\Exception\FileNotFoundException(\sprintf('File path "%s" was not found while creating "%s" object.', $filePath, self::class));
        }
        // real path doesn't work in PHAR: https://www.php.net/manual/en/function.realpath.php
        if (\ECSPrefix20210511\Nette\Utils\Strings::startsWith($filePath, 'phar://')) {
            $relativeFilePath = $filePath;
            $relativeDirectoryPath = \dirname($filePath);
        } else {
            $realPath = \realpath($filePath);
            $relativeFilePath = \rtrim($this->smartFileSystem->makePathRelative($realPath, \getcwd()), '/');
            $relativeDirectoryPath = \dirname($relativeFilePath);
        }
        parent::__construct($filePath, $relativeDirectoryPath, $relativeFilePath);
    }
    /**
     * @return string
     */
    public function getBasenameWithoutSuffix()
    {
        return \pathinfo($this->getFilename())['filename'];
    }
    /**
     * @return string
     */
    public function getSuffix()
    {
        return \pathinfo($this->getFilename(), \PATHINFO_EXTENSION);
    }
    /**
     * @param string[] $suffixes
     * @return bool
     */
    public function hasSuffixes(array $suffixes)
    {
        return \in_array($this->getSuffix(), $suffixes, \true);
    }
    /**
     * @return string
     */
    public function getRealPathWithoutSuffix()
    {
        return \ECSPrefix20210511\Nette\Utils\Strings::replace($this->getRealPath(), self::LAST_SUFFIX_REGEX, '');
    }
    /**
     * @return string
     */
    public function getRelativeFilePath()
    {
        return $this->getRelativePathname();
    }
    /**
     * @return string
     */
    public function getRelativeDirectoryPath()
    {
        return $this->getRelativePath();
    }
    /**
     * @param string $directory
     * @return string
     */
    public function getRelativeFilePathFromDirectory($directory)
    {
        $directory = (string) $directory;
        if (!\file_exists($directory)) {
            throw new \Symplify\SmartFileSystem\Exception\DirectoryNotFoundException(\sprintf('Directory "%s" was not found in %s.', $directory, self::class));
        }
        $relativeFilePath = $this->smartFileSystem->makePathRelative($this->getNormalizedRealPath(), (string) \realpath($directory));
        return \rtrim($relativeFilePath, '/');
    }
    /**
     * @return string
     */
    public function getRelativeFilePathFromCwdInTests()
    {
        // special case for tests
        if (\Symplify\EasyTesting\PHPUnit\StaticPHPUnitEnvironment::isPHPUnitRun()) {
            return $this->getRelativeFilePathFromDirectory(\Symplify\EasyTesting\StaticFixtureSplitter::getTemporaryPath());
        }
        return $this->getRelativeFilePathFromDirectory(\getcwd());
    }
    /**
     * @return string
     */
    public function getRelativeFilePathFromCwd()
    {
        return $this->getRelativeFilePathFromDirectory(\getcwd());
    }
    /**
     * @param string $string
     * @return bool
     */
    public function endsWith($string)
    {
        $string = (string) $string;
        return \ECSPrefix20210511\Nette\Utils\Strings::endsWith($this->getNormalizedRealPath(), $string);
    }
    /**
     * @param string $string
     * @return bool
     */
    public function doesFnmatch($string)
    {
        $string = (string) $string;
        if (\fnmatch($this->normalizePath($string), $this->getNormalizedRealPath())) {
            return \true;
        }
        // in case of relative compare
        return \fnmatch('*/' . $this->normalizePath($string), $this->getNormalizedRealPath());
    }
    /**
     * @return string
     */
    public function getRealPath()
    {
        // for phar compatibility @see https://github.com/rectorphp/rector/commit/e5d7cee69558f7e6b35d995a5ca03fa481b0407c
        return parent::getRealPath() ?: $this->getPathname();
    }
    /**
     * @return string
     */
    public function getRealPathDirectory()
    {
        return \dirname($this->getRealPath());
    }
    /**
     * @param string $partialPath
     * @return bool
     */
    public function startsWith($partialPath)
    {
        $partialPath = (string) $partialPath;
        return \ECSPrefix20210511\Nette\Utils\Strings::startsWith($this->getNormalizedRealPath(), $partialPath);
    }
    /**
     * @return string
     */
    private function getNormalizedRealPath()
    {
        return $this->normalizePath($this->getRealPath());
    }
    /**
     * @param string $path
     * @return string
     */
    private function normalizePath($path)
    {
        $path = (string) $path;
        return \str_replace('\\', '/', $path);
    }
}
