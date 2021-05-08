<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\Symfony\Component\Cache\Traits;

use ECSPrefix20210508\Symfony\Component\Cache\Exception\InvalidArgumentException;
/**
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @internal
 */
trait FilesystemCommonTrait
{
    private $directory;
    private $tmp;
    /**
     * @param string|null $directory
     * @param string $namespace
     */
    private function init($namespace, $directory)
    {
        $namespace = (string) $namespace;
        if (!isset($directory[0])) {
            $directory = \sys_get_temp_dir() . \DIRECTORY_SEPARATOR . 'symfony-cache';
        } else {
            $directory = \realpath($directory) ?: $directory;
        }
        if (isset($namespace[0])) {
            if (\preg_match('#[^-+_.A-Za-z0-9]#', $namespace, $match)) {
                throw new \ECSPrefix20210508\Symfony\Component\Cache\Exception\InvalidArgumentException(\sprintf('Namespace contains "%s" but only characters in [-+_.A-Za-z0-9] are allowed.', $match[0]));
            }
            $directory .= \DIRECTORY_SEPARATOR . $namespace;
        } else {
            $directory .= \DIRECTORY_SEPARATOR . '@';
        }
        if (!\is_dir($directory)) {
            @\mkdir($directory, 0777, \true);
        }
        $directory .= \DIRECTORY_SEPARATOR;
        // On Windows the whole path is limited to 258 chars
        if ('\\' === \DIRECTORY_SEPARATOR && \strlen($directory) > 234) {
            throw new \ECSPrefix20210508\Symfony\Component\Cache\Exception\InvalidArgumentException(\sprintf('Cache directory too long (%s).', $directory));
        }
        $this->directory = $directory;
    }
    /**
     * {@inheritdoc}
     * @param string $namespace
     */
    protected function doClear($namespace)
    {
        $ok = \true;
        foreach ($this->scanHashDir($this->directory) as $file) {
            if ('' !== $namespace && 0 !== \strpos($this->getFileKey($file), $namespace)) {
                continue;
            }
            $ok = ($this->doUnlink($file) || !\file_exists($file)) && $ok;
        }
        return $ok;
    }
    /**
     * {@inheritdoc}
     */
    protected function doDelete(array $ids)
    {
        $ok = \true;
        foreach ($ids as $id) {
            $file = $this->getFile($id);
            $ok = (!\is_file($file) || $this->doUnlink($file) || !\file_exists($file)) && $ok;
        }
        return $ok;
    }
    protected function doUnlink($file)
    {
        return @\unlink($file);
    }
    /**
     * @param string $file
     * @param string $data
     * @param int $expiresAt
     */
    private function write($file, $data, $expiresAt = null)
    {
        $file = (string) $file;
        $data = (string) $data;
        $expiresAt = (int) $expiresAt;
        \set_error_handler(__CLASS__ . '::throwError');
        try {
            if (null === $this->tmp) {
                $this->tmp = $this->directory . \bin2hex(\random_bytes(6));
            }
            try {
                $h = \fopen($this->tmp, 'x');
            } catch (\ErrorException $e) {
                if (\false === \strpos($e->getMessage(), 'File exists')) {
                    throw $e;
                }
                $this->tmp = $this->directory . \bin2hex(\random_bytes(6));
                $h = \fopen($this->tmp, 'x');
            }
            \fwrite($h, $data);
            \fclose($h);
            if (null !== $expiresAt) {
                \touch($this->tmp, $expiresAt);
            }
            return \rename($this->tmp, $file);
        } finally {
            \restore_error_handler();
        }
    }
    /**
     * @param string $id
     * @param bool $mkdir
     * @param string $directory
     */
    private function getFile($id, $mkdir = \false, $directory = null)
    {
        $id = (string) $id;
        $mkdir = (bool) $mkdir;
        $directory = (string) $directory;
        // Use MD5 to favor speed over security, which is not an issue here
        $hash = \str_replace('/', '-', \base64_encode(\hash('md5', static::class . $id, \true)));
        $dir = (isset($directory) ? $directory : $this->directory) . \strtoupper($hash[0] . \DIRECTORY_SEPARATOR . $hash[1] . \DIRECTORY_SEPARATOR);
        if ($mkdir && !\is_dir($dir)) {
            @\mkdir($dir, 0777, \true);
        }
        return $dir . \substr($hash, 2, 20);
    }
    /**
     * @param string $file
     * @return string
     */
    private function getFileKey($file)
    {
        $file = (string) $file;
        return '';
    }
    /**
     * @param string $directory
     * @return \Generator
     */
    private function scanHashDir($directory)
    {
        $directory = (string) $directory;
        if (!\is_dir($directory)) {
            return;
        }
        $chars = '+-ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        for ($i = 0; $i < 38; ++$i) {
            if (!\is_dir($directory . $chars[$i])) {
                continue;
            }
            for ($j = 0; $j < 38; ++$j) {
                if (!\is_dir($dir = $directory . $chars[$i] . \DIRECTORY_SEPARATOR . $chars[$j])) {
                    continue;
                }
                foreach (@\scandir($dir, \SCANDIR_SORT_NONE) ?: [] as $file) {
                    if ('.' !== $file && '..' !== $file) {
                        (yield $dir . \DIRECTORY_SEPARATOR . $file);
                    }
                }
            }
        }
    }
    /**
     * @internal
     */
    public static function throwError($type, $message, $file, $line)
    {
        throw new \ErrorException($message, 0, $type, $file, $line);
    }
    /**
     * @return array
     */
    public function __sleep()
    {
        throw new \BadMethodCallException('Cannot serialize ' . __CLASS__);
    }
    public function __wakeup()
    {
        throw new \BadMethodCallException('Cannot unserialize ' . __CLASS__);
    }
    public function __destruct()
    {
        if (\method_exists(parent::class, '__destruct')) {
            parent::__destruct();
        }
        if (null !== $this->tmp && \is_file($this->tmp)) {
            \unlink($this->tmp);
        }
    }
}
