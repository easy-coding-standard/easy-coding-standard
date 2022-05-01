<?php

declare (strict_types=1);
namespace ECSPrefix20220501\Symplify\VendorPatches\Command;

use ECSPrefix20220501\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20220501\Symfony\Component\Console\Output\OutputInterface;
use ECSPrefix20220501\Symplify\PackageBuilder\Composer\VendorDirProvider;
use ECSPrefix20220501\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use ECSPrefix20220501\Symplify\PackageBuilder\Console\Command\CommandNaming;
use ECSPrefix20220501\Symplify\VendorPatches\Composer\ComposerPatchesConfigurationUpdater;
use ECSPrefix20220501\Symplify\VendorPatches\Console\GenerateCommandReporter;
use ECSPrefix20220501\Symplify\VendorPatches\Differ\PatchDiffer;
use ECSPrefix20220501\Symplify\VendorPatches\Finder\OldToNewFilesFinder;
use ECSPrefix20220501\Symplify\VendorPatches\PatchFileFactory;
final class GenerateCommand extends \ECSPrefix20220501\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand
{
    /**
     * @var \Symplify\VendorPatches\Finder\OldToNewFilesFinder
     */
    private $oldToNewFilesFinder;
    /**
     * @var \Symplify\VendorPatches\Differ\PatchDiffer
     */
    private $patchDiffer;
    /**
     * @var \Symplify\VendorPatches\Composer\ComposerPatchesConfigurationUpdater
     */
    private $composerPatchesConfigurationUpdater;
    /**
     * @var \Symplify\PackageBuilder\Composer\VendorDirProvider
     */
    private $vendorDirProvider;
    /**
     * @var \Symplify\VendorPatches\PatchFileFactory
     */
    private $patchFileFactory;
    /**
     * @var \Symplify\VendorPatches\Console\GenerateCommandReporter
     */
    private $generateCommandReporter;
    public function __construct(\ECSPrefix20220501\Symplify\VendorPatches\Finder\OldToNewFilesFinder $oldToNewFilesFinder, \ECSPrefix20220501\Symplify\VendorPatches\Differ\PatchDiffer $patchDiffer, \ECSPrefix20220501\Symplify\VendorPatches\Composer\ComposerPatchesConfigurationUpdater $composerPatchesConfigurationUpdater, \ECSPrefix20220501\Symplify\PackageBuilder\Composer\VendorDirProvider $vendorDirProvider, \ECSPrefix20220501\Symplify\VendorPatches\PatchFileFactory $patchFileFactory, \ECSPrefix20220501\Symplify\VendorPatches\Console\GenerateCommandReporter $generateCommandReporter)
    {
        $this->oldToNewFilesFinder = $oldToNewFilesFinder;
        $this->patchDiffer = $patchDiffer;
        $this->composerPatchesConfigurationUpdater = $composerPatchesConfigurationUpdater;
        $this->vendorDirProvider = $vendorDirProvider;
        $this->patchFileFactory = $patchFileFactory;
        $this->generateCommandReporter = $generateCommandReporter;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName(\ECSPrefix20220501\Symplify\PackageBuilder\Console\Command\CommandNaming::classToName(self::class));
        $this->setDescription('Generate patches from /vendor directory');
    }
    protected function execute(\ECSPrefix20220501\Symfony\Component\Console\Input\InputInterface $input, \ECSPrefix20220501\Symfony\Component\Console\Output\OutputInterface $output) : int
    {
        $vendorDirectory = $this->vendorDirProvider->provide();
        $oldAndNewFileInfos = $this->oldToNewFilesFinder->find($vendorDirectory);
        $composerExtraPatches = [];
        $addedPatchFilesByPackageName = [];
        foreach ($oldAndNewFileInfos as $oldAndNewFileInfo) {
            if ($oldAndNewFileInfo->isContentIdentical()) {
                $this->generateCommandReporter->reportIdenticalNewAndOldFile($oldAndNewFileInfo);
                continue;
            }
            // write into patches file
            $patchFileRelativePath = $this->patchFileFactory->createPatchFilePath($oldAndNewFileInfo, $vendorDirectory);
            $composerExtraPatches[$oldAndNewFileInfo->getPackageName()][] = $patchFileRelativePath;
            $patchFileAbsolutePath = \dirname($vendorDirectory) . \DIRECTORY_SEPARATOR . $patchFileRelativePath;
            // dump the patch
            $patchDiff = $this->patchDiffer->diff($oldAndNewFileInfo);
            if (\is_file($patchFileAbsolutePath)) {
                $message = \sprintf('File "%s" was updated', $patchFileRelativePath);
                $this->symfonyStyle->note($message);
            } else {
                $message = \sprintf('File "%s" was created', $patchFileRelativePath);
                $this->symfonyStyle->note($message);
            }
            $this->smartFileSystem->dumpFile($patchFileAbsolutePath, $patchDiff);
            $addedPatchFilesByPackageName[$oldAndNewFileInfo->getPackageName()][] = $patchFileRelativePath;
        }
        $this->composerPatchesConfigurationUpdater->updateComposerJsonAndPrint(\getcwd() . '/composer.json', $composerExtraPatches);
        if ($addedPatchFilesByPackageName !== []) {
            $message = \sprintf('Great! %d new patch files added', \count($addedPatchFilesByPackageName));
            $this->symfonyStyle->success($message);
        } else {
            $this->symfonyStyle->success('No new patches were added');
        }
        return self::SUCCESS;
    }
}
