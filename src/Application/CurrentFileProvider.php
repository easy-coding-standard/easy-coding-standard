<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application;

use Symfony\Component\Finder\SplFileInfo;

final class CurrentFileProvider
{
    /**
     * @var SplFileInfo
     */
    private $fileInfo;

    public function setFileInfo(SplFileInfo $fileInfo): void
    {
        $this->fileInfo = $fileInfo;
    }

    public function getFileInfo(): SplFileInfo
    {
        return $this->fileInfo;
    }
}
