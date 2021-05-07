<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Component\Cache\Adapter;

use ECSPrefix20210507\Symfony\Component\Cache\Marshaller\MarshallerInterface;
use ECSPrefix20210507\Symfony\Component\Cache\Marshaller\TagAwareMarshaller;
use ECSPrefix20210507\Symfony\Component\Cache\PruneableInterface;
use ECSPrefix20210507\Symfony\Component\Cache\Traits\FilesystemTrait;
/**
 * Stores tag id <> cache id relationship as a symlink, and lookup on invalidation calls.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 * @author André Rømcke <andre.romcke+symfony@gmail.com>
 */
class FilesystemTagAwareAdapter extends \ECSPrefix20210507\Symfony\Component\Cache\Adapter\AbstractTagAwareAdapter implements \ECSPrefix20210507\Symfony\Component\Cache\PruneableInterface
{
    use FilesystemTrait {
        doClear as private doClearCache;
        doSave as private doSaveCache;
    }
    /**
     * Folder used for tag symlinks.
     */
    const TAG_FOLDER = 'tags';
    /**
     * @param string $namespace
     * @param int $defaultLifetime
     * @param string $directory
     * @param \Symfony\Component\Cache\Marshaller\MarshallerInterface $marshaller
     */
    public function __construct($namespace = '', $defaultLifetime = 0, $directory = null, $marshaller = null)
    {
        $this->marshaller = new \ECSPrefix20210507\Symfony\Component\Cache\Marshaller\TagAwareMarshaller($marshaller);
        parent::__construct('', $defaultLifetime);
        $this->init($namespace, $directory);
    }
    /**
     * {@inheritdoc}
     * @param string $namespace
     */
    protected function doClear($namespace)
    {
        $ok = $this->doClearCache($namespace);
        if ('' !== $namespace) {
            return $ok;
        }
        \set_error_handler(static function () {
        });
        $chars = '+-ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        try {
            foreach ($this->scanHashDir($this->directory . self::TAG_FOLDER . \DIRECTORY_SEPARATOR) as $dir) {
                if (\rename($dir, $renamed = \substr_replace($dir, \bin2hex(\random_bytes(4)), -8))) {
                    $dir = $renamed . \DIRECTORY_SEPARATOR;
                } else {
                    $dir .= \DIRECTORY_SEPARATOR;
                    $renamed = null;
                }
                for ($i = 0; $i < 38; ++$i) {
                    if (!\is_dir($dir . $chars[$i])) {
                        continue;
                    }
                    for ($j = 0; $j < 38; ++$j) {
                        if (!\is_dir($d = $dir . $chars[$i] . \DIRECTORY_SEPARATOR . $chars[$j])) {
                            continue;
                        }
                        foreach (\scandir($d, \SCANDIR_SORT_NONE) ?: [] as $link) {
                            if ('.' !== $link && '..' !== $link && (null !== $renamed || !\realpath($d . \DIRECTORY_SEPARATOR . $link))) {
                                \unlink($d . \DIRECTORY_SEPARATOR . $link);
                            }
                        }
                        null === $renamed ?: \rmdir($d);
                    }
                    null === $renamed ?: \rmdir($dir . $chars[$i]);
                }
                null === $renamed ?: \rmdir($renamed);
            }
        } finally {
            \restore_error_handler();
        }
        return $ok;
    }
    /**
     * {@inheritdoc}
     * @param mixed[] $addTagData
     * @param mixed[] $removeTagData
     * @param int $lifetime
     * @return mixed[]
     */
    protected function doSave(array $values, $lifetime, $addTagData = [], $removeTagData = [])
    {
        $failed = $this->doSaveCache($values, $lifetime);
        // Add Tags as symlinks
        foreach ($addTagData as $tagId => $ids) {
            $tagFolder = $this->getTagFolder($tagId);
            foreach ($ids as $id) {
                if ($failed && \in_array($id, $failed, \true)) {
                    continue;
                }
                $file = $this->getFile($id);
                if (!@\symlink($file, $tagLink = $this->getFile($id, \true, $tagFolder)) && !\is_link($tagLink)) {
                    @\unlink($file);
                    $failed[] = $id;
                }
            }
        }
        // Unlink removed Tags
        foreach ($removeTagData as $tagId => $ids) {
            $tagFolder = $this->getTagFolder($tagId);
            foreach ($ids as $id) {
                if ($failed && \in_array($id, $failed, \true)) {
                    continue;
                }
                @\unlink($this->getFile($id, \false, $tagFolder));
            }
        }
        return $failed;
    }
    /**
     * {@inheritdoc}
     * @return mixed[]
     */
    protected function doDeleteYieldTags(array $ids)
    {
        foreach ($ids as $id) {
            $file = $this->getFile($id);
            if (!\is_file($file) || !($h = @\fopen($file, 'r'))) {
                continue;
            }
            if ((\PHP_VERSION_ID >= 70300 || '\\' !== \DIRECTORY_SEPARATOR) && !@\unlink($file)) {
                \fclose($h);
                continue;
            }
            $meta = isset(\explode("\n", \fread($h, 4096), 3)[2]) ? \explode("\n", \fread($h, 4096), 3)[2] : '';
            // detect the compact format used in marshall() using magic numbers in the form 9D-..-..-..-..-00-..-..-..-5F
            if (13 < \strlen($meta) && "�" === $meta[0] && "\0" === $meta[5] && "_" === $meta[9]) {
                $meta[9] = "\0";
                $tagLen = \unpack('Nlen', $meta, 9)['len'];
                $meta = \substr($meta, 13, $tagLen);
                if (0 < ($tagLen -= \strlen($meta))) {
                    $meta .= \fread($h, $tagLen);
                }
                try {
                    (yield $id => '' === $meta ? [] : $this->marshaller->unmarshall($meta));
                } catch (\Exception $e) {
                    (yield $id => []);
                }
            }
            \fclose($h);
            if (\PHP_VERSION_ID < 70300 && '\\' === \DIRECTORY_SEPARATOR) {
                @\unlink($file);
            }
        }
    }
    /**
     * {@inheritdoc}
     * @return bool
     */
    protected function doDeleteTagRelations(array $tagData)
    {
        foreach ($tagData as $tagId => $idList) {
            $tagFolder = $this->getTagFolder($tagId);
            foreach ($idList as $id) {
                @\unlink($this->getFile($id, \false, $tagFolder));
            }
        }
        return \true;
    }
    /**
     * {@inheritdoc}
     * @return bool
     */
    protected function doInvalidate(array $tagIds)
    {
        foreach ($tagIds as $tagId) {
            if (!\is_dir($tagFolder = $this->getTagFolder($tagId))) {
                continue;
            }
            \set_error_handler(static function () {
            });
            try {
                if (\rename($tagFolder, $renamed = \substr_replace($tagFolder, \bin2hex(\random_bytes(4)), -9))) {
                    $tagFolder = $renamed . \DIRECTORY_SEPARATOR;
                } else {
                    $renamed = null;
                }
                foreach ($this->scanHashDir($tagFolder) as $itemLink) {
                    \unlink(\realpath($itemLink) ?: $itemLink);
                    \unlink($itemLink);
                }
                if (null === $renamed) {
                    continue;
                }
                $chars = '+-ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                for ($i = 0; $i < 38; ++$i) {
                    for ($j = 0; $j < 38; ++$j) {
                        \rmdir($tagFolder . $chars[$i] . \DIRECTORY_SEPARATOR . $chars[$j]);
                    }
                    \rmdir($tagFolder . $chars[$i]);
                }
                \rmdir($renamed);
            } finally {
                \restore_error_handler();
            }
        }
        return \true;
    }
    /**
     * @param string $tagId
     * @return string
     */
    private function getTagFolder($tagId)
    {
        return $this->getFile($tagId, \false, $this->directory . self::TAG_FOLDER . \DIRECTORY_SEPARATOR) . \DIRECTORY_SEPARATOR;
    }
}
