<?php

namespace ECSPrefix20210514\Symplify\SmartFileSystem;

use ECSPrefix20210514\Symplify\SmartFileSystem\Exception\DirectoryNotFoundException;
use ECSPrefix20210514\Symplify\SmartFileSystem\Exception\FileNotFoundException;
final class FileSystemGuard
{
    /**
     * @return void
     * @param string $file
     * @param string $location
     */
    public function ensureFileExists($file, $location)
    {
        $file = (string) $file;
        $location = (string) $location;
        if (\file_exists($file)) {
            return;
        }
        throw new \ECSPrefix20210514\Symplify\SmartFileSystem\Exception\FileNotFoundException(\sprintf('File "%s" not found in "%s".', $file, $location));
    }
    /**
     * @return void
     * @param string $directory
     * @param string $extraMessage
     */
    public function ensureDirectoryExists($directory, $extraMessage = '')
    {
        $directory = (string) $directory;
        $extraMessage = (string) $extraMessage;
        if (\is_dir($directory) && \file_exists($directory)) {
            return;
        }
        $message = \sprintf('Directory "%s" was not found.', $directory);
        if ($extraMessage !== '') {
            $message .= ' ' . $extraMessage;
        }
        throw new \ECSPrefix20210514\Symplify\SmartFileSystem\Exception\DirectoryNotFoundException($message);
    }
}
