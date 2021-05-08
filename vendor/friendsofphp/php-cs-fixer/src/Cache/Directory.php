<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace PhpCsFixer\Cache;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class Directory implements \PhpCsFixer\Cache\DirectoryInterface
{
    /**
     * @var string
     */
    private $directoryName;
    /**
     * @param string $directoryName
     */
    public function __construct($directoryName)
    {
        if (\is_object($directoryName)) {
            $directoryName = (string) $directoryName;
        }
        $this->directoryName = $directoryName;
    }
    /**
     * @param string $file
     */
    public function getRelativePathTo($file) : string
    {
        if (\is_object($file)) {
            $file = (string) $file;
        }
        $file = $this->normalizePath($file);
        if ('' === $this->directoryName || 0 !== \stripos($file, $this->directoryName . \DIRECTORY_SEPARATOR)) {
            return $file;
        }
        return \substr($file, \strlen($this->directoryName) + 1);
    }
    /**
     * @param string $path
     */
    private function normalizePath($path) : string
    {
        if (\is_object($path)) {
            $path = (string) $path;
        }
        return \str_replace(['\\', '/'], \DIRECTORY_SEPARATOR, $path);
    }
}
