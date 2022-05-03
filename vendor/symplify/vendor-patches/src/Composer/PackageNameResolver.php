<?php

declare (strict_types=1);
namespace ECSPrefix20220503\Symplify\VendorPatches\Composer;

use ECSPrefix20220503\Symplify\SmartFileSystem\FileSystemGuard;
use ECSPrefix20220503\Symplify\SmartFileSystem\Json\JsonFileSystem;
use ECSPrefix20220503\Symplify\SmartFileSystem\SmartFileInfo;
use ECSPrefix20220503\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
use ECSPrefix20220503\Symplify\VendorPatches\FileSystem\PathResolver;
/**
 * @see \Symplify\VendorPatches\Tests\Composer\PackageNameResolverTest
 */
final class PackageNameResolver
{
    /**
     * @var \Symplify\SmartFileSystem\Json\JsonFileSystem
     */
    private $jsonFileSystem;
    /**
     * @var \Symplify\VendorPatches\FileSystem\PathResolver
     */
    private $pathResolver;
    /**
     * @var \Symplify\SmartFileSystem\FileSystemGuard
     */
    private $fileSystemGuard;
    public function __construct(\ECSPrefix20220503\Symplify\SmartFileSystem\Json\JsonFileSystem $jsonFileSystem, \ECSPrefix20220503\Symplify\VendorPatches\FileSystem\PathResolver $pathResolver, \ECSPrefix20220503\Symplify\SmartFileSystem\FileSystemGuard $fileSystemGuard)
    {
        $this->jsonFileSystem = $jsonFileSystem;
        $this->pathResolver = $pathResolver;
        $this->fileSystemGuard = $fileSystemGuard;
    }
    public function resolveFromFileInfo(\ECSPrefix20220503\Symplify\SmartFileSystem\SmartFileInfo $vendorFile) : string
    {
        $packageComposerJsonFilePath = $this->getPackageComposerJsonFilePath($vendorFile);
        $composerJson = $this->jsonFileSystem->loadFilePathToJson($packageComposerJsonFilePath);
        if (!isset($composerJson['name'])) {
            throw new \ECSPrefix20220503\Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
        }
        return $composerJson['name'];
    }
    private function getPackageComposerJsonFilePath(\ECSPrefix20220503\Symplify\SmartFileSystem\SmartFileInfo $vendorFileInfo) : string
    {
        $vendorPackageDirectory = $this->pathResolver->resolveVendorDirectory($vendorFileInfo);
        $packageComposerJsonFilePath = $vendorPackageDirectory . '/composer.json';
        $this->fileSystemGuard->ensureFileExists($packageComposerJsonFilePath, __METHOD__);
        return $packageComposerJsonFilePath;
    }
}
