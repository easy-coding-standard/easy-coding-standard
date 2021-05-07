<?php

namespace ECSPrefix20210507\Nette\Utils;

use ECSPrefix20210507\Nette;
/**
 * File system tool.
 */
final class FileSystem
{
    use Nette\StaticClass;
    /**
     * Creates a directory if it doesn't exist.
     * @throws Nette\IOException  on error occurred
     * @return void
     * @param string $dir
     * @param int $mode
     */
    public static function createDir($dir, $mode = 0777)
    {
        if (!\is_dir($dir) && !@\mkdir($dir, $mode, \true) && !\is_dir($dir)) {
            // @ - dir may already exist
            throw new \ECSPrefix20210507\Nette\IOException("Unable to create directory '{$dir}' with mode " . \decoct($mode) . '. ' . \ECSPrefix20210507\Nette\Utils\Helpers::getLastError());
        }
    }
    /**
     * Copies a file or a directory. Overwrites existing files and directories by default.
     * @throws Nette\IOException  on error occurred
     * @throws Nette\InvalidStateException  if $overwrite is set to false and destination already exists
     * @return void
     * @param string $origin
     * @param string $target
     * @param bool $overwrite
     */
    public static function copy($origin, $target, $overwrite = \true)
    {
        if (\stream_is_local($origin) && !\file_exists($origin)) {
            throw new \ECSPrefix20210507\Nette\IOException("File or directory '{$origin}' not found.");
        } elseif (!$overwrite && \file_exists($target)) {
            throw new \ECSPrefix20210507\Nette\InvalidStateException("File or directory '{$target}' already exists.");
        } elseif (\is_dir($origin)) {
            static::createDir($target);
            foreach (new \FilesystemIterator($target) as $item) {
                static::delete($item->getPathname());
            }
            foreach ($iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($origin, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST) as $item) {
                if ($item->isDir()) {
                    static::createDir($target . '/' . $iterator->getSubPathName());
                } else {
                    static::copy($item->getPathname(), $target . '/' . $iterator->getSubPathName());
                }
            }
        } else {
            static::createDir(\dirname($target));
            if (($s = @\fopen($origin, 'rb')) && ($d = @\fopen($target, 'wb')) && @\stream_copy_to_stream($s, $d) === \false) {
                // @ is escalated to exception
                throw new \ECSPrefix20210507\Nette\IOException("Unable to copy file '{$origin}' to '{$target}'. " . \ECSPrefix20210507\Nette\Utils\Helpers::getLastError());
            }
        }
    }
    /**
     * Deletes a file or directory if exists.
     * @throws Nette\IOException  on error occurred
     * @return void
     * @param string $path
     */
    public static function delete($path)
    {
        if (\is_file($path) || \is_link($path)) {
            $func = \DIRECTORY_SEPARATOR === '\\' && \is_dir($path) ? 'rmdir' : 'unlink';
            if (!@$func($path)) {
                // @ is escalated to exception
                throw new \ECSPrefix20210507\Nette\IOException("Unable to delete '{$path}'. " . \ECSPrefix20210507\Nette\Utils\Helpers::getLastError());
            }
        } elseif (\is_dir($path)) {
            foreach (new \FilesystemIterator($path) as $item) {
                static::delete($item->getPathname());
            }
            if (!@\rmdir($path)) {
                // @ is escalated to exception
                throw new \ECSPrefix20210507\Nette\IOException("Unable to delete directory '{$path}'. " . \ECSPrefix20210507\Nette\Utils\Helpers::getLastError());
            }
        }
    }
    /**
     * Renames or moves a file or a directory. Overwrites existing files and directories by default.
     * @throws Nette\IOException  on error occurred
     * @throws Nette\InvalidStateException  if $overwrite is set to false and destination already exists
     * @return void
     * @param string $origin
     * @param string $target
     * @param bool $overwrite
     */
    public static function rename($origin, $target, $overwrite = \true)
    {
        if (!$overwrite && \file_exists($target)) {
            throw new \ECSPrefix20210507\Nette\InvalidStateException("File or directory '{$target}' already exists.");
        } elseif (!\file_exists($origin)) {
            throw new \ECSPrefix20210507\Nette\IOException("File or directory '{$origin}' not found.");
        } else {
            static::createDir(\dirname($target));
            if (\realpath($origin) !== \realpath($target)) {
                static::delete($target);
            }
            if (!@\rename($origin, $target)) {
                // @ is escalated to exception
                throw new \ECSPrefix20210507\Nette\IOException("Unable to rename file or directory '{$origin}' to '{$target}'. " . \ECSPrefix20210507\Nette\Utils\Helpers::getLastError());
            }
        }
    }
    /**
     * Reads the content of a file.
     * @throws Nette\IOException  on error occurred
     * @param string $file
     * @return string
     */
    public static function read($file)
    {
        $content = @\file_get_contents($file);
        // @ is escalated to exception
        if ($content === \false) {
            throw new \ECSPrefix20210507\Nette\IOException("Unable to read file '{$file}'. " . \ECSPrefix20210507\Nette\Utils\Helpers::getLastError());
        }
        return $content;
    }
    /**
     * Writes the string to a file.
     * @throws Nette\IOException  on error occurred
     * @param int|null $mode
     * @return void
     * @param string $file
     * @param string $content
     */
    public static function write($file, $content, $mode = 0666)
    {
        static::createDir(\dirname($file));
        if (@\file_put_contents($file, $content) === \false) {
            // @ is escalated to exception
            throw new \ECSPrefix20210507\Nette\IOException("Unable to write file '{$file}'. " . \ECSPrefix20210507\Nette\Utils\Helpers::getLastError());
        }
        if ($mode !== null && !@\chmod($file, $mode)) {
            // @ is escalated to exception
            throw new \ECSPrefix20210507\Nette\IOException("Unable to chmod file '{$file}' to mode " . \decoct($mode) . '. ' . \ECSPrefix20210507\Nette\Utils\Helpers::getLastError());
        }
    }
    /**
     * Determines if the path is absolute.
     * @param string $path
     * @return bool
     */
    public static function isAbsolute($path)
    {
        return (bool) \preg_match('#([a-z]:)?[/\\\\]|[a-z][a-z0-9+.-]*://#Ai', $path);
    }
    /**
     * Normalizes `..` and `.` and directory separators in path.
     * @param string $path
     * @return string
     */
    public static function normalizePath($path)
    {
        $parts = $path === '' ? [] : \preg_split('~[/\\\\]+~', $path);
        $res = [];
        foreach ($parts as $part) {
            if ($part === '..' && $res && \end($res) !== '..' && \end($res) !== '') {
                \array_pop($res);
            } elseif ($part !== '.') {
                $res[] = $part;
            }
        }
        return $res === [''] ? \DIRECTORY_SEPARATOR : \implode(\DIRECTORY_SEPARATOR, $res);
    }
    /**
     * Joins all segments of the path and normalizes the result.
     * @param string ...$paths
     * @return string
     */
    public static function joinPaths(...$paths)
    {
        return self::normalizePath(\implode('/', $paths));
    }
}
