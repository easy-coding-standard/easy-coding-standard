<?php

namespace Symplify\SmartFileSystem;

use Symplify\SmartFileSystem\Exception\DirectoryNotFoundException;
use Symplify\SmartFileSystem\Exception\FileNotFoundException;
final class FileSystemGuard
{
    /**
     * @return void
     * @param string $file
     */
    public function ensureFileExists($file, string $location)
    {
        if (\is_object($file)) {
            $file = (string) $file;
        }
        if (\file_exists($file)) {
            return;
        }
        throw new \Symplify\SmartFileSystem\Exception\FileNotFoundException(\sprintf('File "%s" not found in "%s".', $file, $location));
    }
    /**
     * @return void
     * @param string $directory
     */
    public function ensureDirectoryExists($directory, string $extraMessage = '')
    {
        if (\is_object($directory)) {
            $directory = (string) $directory;
        }
        if (\is_dir($directory) && \file_exists($directory)) {
            return;
        }
        $message = \sprintf('Directory "%s" was not found.', $directory);
        if ($extraMessage !== '') {
            $message .= ' ' . $extraMessage;
        }
        throw new \Symplify\SmartFileSystem\Exception\DirectoryNotFoundException($message);
    }
}
