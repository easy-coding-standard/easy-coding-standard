<?php

declare (strict_types=1);
namespace ECSPrefix20220521\Symplify\VendorPatches\Console;

use ECSPrefix20220521\Symfony\Component\Console\Style\SymfonyStyle;
use ECSPrefix20220521\Symplify\VendorPatches\ValueObject\OldAndNewFileInfo;
final class GenerateCommandReporter
{
    /**
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    public function __construct(\ECSPrefix20220521\Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }
    public function reportIdenticalNewAndOldFile(\ECSPrefix20220521\Symplify\VendorPatches\ValueObject\OldAndNewFileInfo $oldAndNewFileInfo) : void
    {
        $message = \sprintf('Files "%s" and "%s" have the same content. Did you forgot to change it?', $oldAndNewFileInfo->getOldFileRelativePath(), $oldAndNewFileInfo->getNewFileRelativePath());
        $this->symfonyStyle->warning($message);
    }
}
