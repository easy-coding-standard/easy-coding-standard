<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\File\Provider;

use SplFileInfo;
use Symplify\EasyCodingStandard\Finder\SourceFinder;
use Symplify\EasyCodingStandard\SniffRunner\File\File;
use Symplify\EasyCodingStandard\SniffRunner\File\FileFactory;

final class FilesProvider
{
    /**
     * @var SourceFinder
     */
    private $sourceFinder;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var File[][]
     */
    private $filesBySource = [];

    public function __construct(SourceFinder $sourceFinder, FileFactory $fileFactory)
    {
        $this->sourceFinder = $sourceFinder;
        $this->fileFactory = $fileFactory;
    }

    /**
     * @return File[]
     */
    public function getFilesForSource(array $source, bool $isFixer) : array
    {
        $sourceHash = md5(json_encode($source));
        if (isset($this->filesBySource[$sourceHash])) {
            return $this->filesBySource[$sourceHash];
        }

        return $this->filesBySource[$sourceHash] = $this->wrapFilesToValueObjects(
            $this->sourceFinder->find($source),
            $isFixer
        );
    }

    /**
     * @param SplFileInfo[] $fileInfos
     * @param bool $isFixer
     * @return File[]
     */
    private function wrapFilesToValueObjects(array $fileInfos, bool $isFixer) : array
    {
        $files = [];
        foreach ($fileInfos as $name => $fileInfo) {
            $files[$name] = $this->fileFactory->create($name, $isFixer);
        }

        return $files;
    }
}
