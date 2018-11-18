<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application;

use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class CurrentFileProvider
{
    /**
     * @var SmartFileInfo|null
     */
    private $smartFileInfo;

    public function setFileInfo(SmartFileInfo $smartFileInfo): void
    {
        $this->smartFileInfo = $smartFileInfo;
    }

    public function getFileInfo(): ?SmartFileInfo
    {
        return $this->smartFileInfo;
    }
}
