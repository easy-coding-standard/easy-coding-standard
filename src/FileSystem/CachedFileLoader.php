<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FileSystem;

use SplFileInfo;

final class CachedFileLoader
{
    /**
     * @var string[]
     */
    private $fileContentByHash = [];

    public function getFileContent(SplFileInfo $fileInfo): string
    {
        $fileHash = md5_file($fileInfo->getRealPath());

        if (isset($this->fileContentByHash[$fileHash])) {
            return $this->fileContentByHash[$fileHash];
        }

        return $this->fileContentByHash[$fileHash] = (string) file_get_contents($fileInfo->getRealPath());
    }
}
