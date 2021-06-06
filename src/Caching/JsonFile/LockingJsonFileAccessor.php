<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Caching\JsonFile;

use ECSPrefix20210606\Nette\Utils\JsonException;
use ECSPrefix20210606\Symfony\Component\Filesystem\Exception\IOException;
use Symplify\EasyCodingStandard\Caching\Journal\DataContainer;
final class LockingJsonFileAccessor
{
    /**
     * @var string
     */
    private $filePath;
    /**
     * @var resource|null
     */
    private $fileResource;
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }
    public function exists() : bool
    {
        return \file_exists($this->filePath);
    }
    public function isWritable() : bool
    {
        return \is_writable($this->filePath);
    }
    /**
     * @throws JsonException
     */
    public function openAndRead() : \Symplify\EasyCodingStandard\Caching\Journal\DataContainer
    {
        if ($this->fileResource === null) {
            $this->fileResource = \fopen($this->filePath, 'r+') ?: null;
            if ($this->fileResource === null) {
                throw new \ECSPrefix20210606\Symfony\Component\Filesystem\Exception\IOException(\sprintf("Failed to open file '%s' for reading & writing", $this->filePath));
            }
            $result = \flock($this->fileResource, \LOCK_EX | \LOCK_NB);
            if (!$result) {
                throw new \ECSPrefix20210606\Symfony\Component\Filesystem\Exception\IOException(\sprintf("Failed to acquire exclusive lock on the file '%s'", $this->filePath));
            }
        }
        $filesize = (int) \filesize($this->filePath);
        $rawData = \fread($this->fileResource, $filesize);
        if ($rawData === \false) {
            throw new \ECSPrefix20210606\Symfony\Component\Filesystem\Exception\IOException(\sprintf("Could not read contents from the file '%s'", $this->filePath));
        }
        return \Symplify\EasyCodingStandard\Caching\Journal\DataContainer::fromJson($rawData);
    }
    /**
     * @return void
     */
    public function writeAndClose(\Symplify\EasyCodingStandard\Caching\Journal\DataContainer $dataContainer)
    {
        if ($this->fileResource === null) {
            throw new \ECSPrefix20210606\Symfony\Component\Filesystem\Exception\IOException('Trying to write file without first reading it');
        }
        $rawData = $dataContainer->toJson();
        $byteLength = \mb_strlen($rawData, '8bit');
        $result = \ftruncate($this->fileResource, $byteLength);
        if (!$result) {
            throw new \ECSPrefix20210606\Symfony\Component\Filesystem\Exception\IOException(\sprintf("Could not truncate the contents of file '%s' before writing new data to it", $this->filePath));
        }
        $result = \fseek($this->fileResource, 0);
        if ($result === -1) {
            throw new \ECSPrefix20210606\Symfony\Component\Filesystem\Exception\IOException(\sprintf("Could set the pointer to the beginning of the file '%s'", $this->filePath));
        }
        $result = \fwrite($this->fileResource, $rawData);
        if ($result === \false) {
            throw new \ECSPrefix20210606\Symfony\Component\Filesystem\Exception\IOException(\sprintf("Could not write contents into file '%s'", $this->filePath));
        }
        $result = \flock($this->fileResource, \LOCK_UN);
        if (!$result) {
            throw new \ECSPrefix20210606\Symfony\Component\Filesystem\Exception\IOException(\sprintf("Failed to release lock on file '%s'", $this->filePath));
        }
        $result = \fflush($this->fileResource);
        if (!$result) {
            throw new \ECSPrefix20210606\Symfony\Component\Filesystem\Exception\IOException(\sprintf("Failed to flush written data to file '%s'", $this->filePath));
        }
        $result = \fclose($this->fileResource);
        if (!$result) {
            throw new \ECSPrefix20210606\Symfony\Component\Filesystem\Exception\IOException(\sprintf("Failed to close file '%s'", $this->filePath));
        }
        $this->fileResource = null;
    }
}
