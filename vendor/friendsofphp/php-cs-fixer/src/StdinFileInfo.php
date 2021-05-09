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
namespace PhpCsFixer;

/**
 * @author Davi Koscianski Vidal <davividal@gmail.com>
 *
 * @internal
 */
final class StdinFileInfo extends \SplFileInfo
{
    public function __construct()
    {
    }
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getRealPath();
    }
    /**
     * @return string
     */
    public function getRealPath()
    {
        // So file_get_contents & friends will work.
        // Warning - this stream is not seekable, so `file_get_contents` will work only once! Consider using `FileReader`.
        return 'php://stdin';
    }
    /**
     * @return int
     */
    public function getATime()
    {
        return 0;
    }
    /**
     * @return string
     */
    public function getBasename($suffix = null)
    {
        return $this->getFilename();
    }
    /**
     * @return int
     */
    public function getCTime()
    {
        return 0;
    }
    /**
     * @return string
     */
    public function getExtension()
    {
        return '.php';
    }
    /**
     * @return \SplFileInfo
     */
    public function getFileInfo($className = null)
    {
        throw new \BadMethodCallException(\sprintf('Method "%s" is not implemented.', __METHOD__));
    }
    /**
     * @return string
     */
    public function getFilename()
    {
        /*
         * Useful so fixers depending on PHP-only files still work.
         *
         * The idea to use STDIN is to parse PHP-only files, so we can
         * assume that there will be always a PHP file out there.
         */
        return 'stdin.php';
    }
    /**
     * @return int
     */
    public function getGroup()
    {
        return 0;
    }
    /**
     * @return int
     */
    public function getInode()
    {
        return 0;
    }
    /**
     * @return string
     */
    public function getLinkTarget()
    {
        return '';
    }
    /**
     * @return int
     */
    public function getMTime()
    {
        return 0;
    }
    /**
     * @return int
     */
    public function getOwner()
    {
        return 0;
    }
    /**
     * @return string
     */
    public function getPath()
    {
        return '';
    }
    /**
     * @return \SplFileInfo
     */
    public function getPathInfo($className = null)
    {
        throw new \BadMethodCallException(\sprintf('Method "%s" is not implemented.', __METHOD__));
    }
    /**
     * @return string
     */
    public function getPathname()
    {
        return $this->getFilename();
    }
    /**
     * @return int
     */
    public function getPerms()
    {
        return 0;
    }
    /**
     * @return int
     */
    public function getSize()
    {
        return 0;
    }
    /**
     * @return string
     */
    public function getType()
    {
        return 'file';
    }
    /**
     * @return bool
     */
    public function isDir()
    {
        return \false;
    }
    /**
     * @return bool
     */
    public function isExecutable()
    {
        return \false;
    }
    /**
     * @return bool
     */
    public function isFile()
    {
        return \true;
    }
    /**
     * @return bool
     */
    public function isLink()
    {
        return \false;
    }
    /**
     * @return bool
     */
    public function isReadable()
    {
        return \true;
    }
    /**
     * @return bool
     */
    public function isWritable()
    {
        return \false;
    }
    /**
     * @return \SplFileObject
     */
    public function openFile($openMode = 'r', $useIncludePath = \false, $context = null)
    {
        throw new \BadMethodCallException(\sprintf('Method "%s" is not implemented.', __METHOD__));
    }
    /**
     * @return void
     */
    public function setFileClass($className = null)
    {
    }
    /**
     * @return void
     */
    public function setInfoClass($className = null)
    {
    }
}
