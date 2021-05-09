<?php

namespace Symplify\SmartFileSystem;

/**
 * @see \Symplify\SmartFileSystem\Tests\FileSystemFilter\FileSystemFilterTest
 */
final class FileSystemFilter
{
    /**
     * @param string[] $filesAndDirectories
     * @return mixed[]
     */
    public function filterDirectories(array $filesAndDirectories)
    {
        $directories = \array_filter($filesAndDirectories, function (string $path) : bool {
            return !\is_file($path);
        });
        return \array_values($directories);
    }
    /**
     * @param string[] $filesAndDirectories
     * @return mixed[]
     */
    public function filterFiles(array $filesAndDirectories)
    {
        $files = \array_filter($filesAndDirectories, function (string $path) : bool {
            return \is_file($path);
        });
        return \array_values($files);
    }
}
