<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Caching;

use ECSPrefix20210606\Nette\Caching\Cache;
use ECSPrefix20210606\Nette\Caching\Storages\Journal;
use ECSPrefix20210606\Symfony\Component\Filesystem\Exception\IOException;
use Symplify\EasyCodingStandard\Caching\Journal\DataContainer;
use Symplify\EasyCodingStandard\Caching\Journal\PriorityManager;
use Symplify\EasyCodingStandard\Caching\Journal\TagManager;
use Symplify\EasyCodingStandard\Caching\JsonFile\LockingJsonFileAccessor;
use ECSPrefix20210606\Symplify\SmartFileSystem\SmartFileSystem;
class JsonFileJournal implements \ECSPrefix20210606\Nette\Caching\Storages\Journal
{
    /** @var LockingJsonFileAccessor */
    private $fileAccessor;
    /** @var DataContainer */
    private $journal;
    public function __construct(string $journalFilePath = 'journal.json')
    {
        $this->fileAccessor = new \Symplify\EasyCodingStandard\Caching\JsonFile\LockingJsonFileAccessor($journalFilePath);
        if ($this->fileAccessor->exists() && !$this->fileAccessor->isWritable()) {
            throw new \ECSPrefix20210606\Symfony\Component\Filesystem\Exception\IOException("Cache journal file '{$journalFilePath}' is not writable");
        }
        if (!$this->fileAccessor->exists()) {
            $filesystem = new \ECSPrefix20210606\Symplify\SmartFileSystem\SmartFileSystem();
            $emptyContainer = new \Symplify\EasyCodingStandard\Caching\Journal\DataContainer();
            $filesystem->dumpFile($journalFilePath, $emptyContainer->toJson());
        }
    }
    /**
     * @return void
     */
    public function write(string $key, array $dependencies)
    {
        $this->journal = $this->fileAccessor->openAndRead();
        if (isset($dependencies[\ECSPrefix20210606\Nette\Caching\Cache::TAGS]) && \count($dependencies[\ECSPrefix20210606\Nette\Caching\Cache::TAGS]) > 0) {
            $tagManager = new \Symplify\EasyCodingStandard\Caching\Journal\TagManager($this->journal);
            $tagManager->deleteTagsForKey($key);
            $tagManager->addTagsForKey($key, $dependencies[\ECSPrefix20210606\Nette\Caching\Cache::TAGS]);
        }
        if (isset($dependencies[\ECSPrefix20210606\Nette\Caching\Cache::PRIORITY])) {
            $priorityManager = new \Symplify\EasyCodingStandard\Caching\Journal\PriorityManager($this->journal);
            $priorityManager->unsetPriority($key);
            $priorityManager->setPriority($key, (int) $dependencies[\ECSPrefix20210606\Nette\Caching\Cache::PRIORITY]);
        }
        $this->fileAccessor->writeAndClose($this->journal);
    }
    /**
     * @param array $conditions
     *
     * @return mixed[]|null
     */
    public function clean(array $conditions)
    {
        $this->journal = $this->fileAccessor->openAndRead();
        if (isset($conditions[\ECSPrefix20210606\Nette\Caching\Cache::ALL])) {
            $this->fileAccessor->writeAndClose(new \Symplify\EasyCodingStandard\Caching\Journal\DataContainer());
            return null;
        }
        $tagManager = new \Symplify\EasyCodingStandard\Caching\Journal\TagManager($this->journal);
        $priorityManager = new \Symplify\EasyCodingStandard\Caching\Journal\PriorityManager($this->journal);
        $keys = [];
        if (isset($conditions[\ECSPrefix20210606\Nette\Caching\Cache::TAGS]) && \count($conditions[\ECSPrefix20210606\Nette\Caching\Cache::TAGS]) > 0) {
            $keys += $tagManager->getKeysByTags($conditions[\ECSPrefix20210606\Nette\Caching\Cache::TAGS]);
        }
        if (isset($conditions[\ECSPrefix20210606\Nette\Caching\Cache::PRIORITY]) && \count($conditions[\ECSPrefix20210606\Nette\Caching\Cache::PRIORITY]) > 0) {
            $keys += $priorityManager->getKeysByPriority($conditions[\ECSPrefix20210606\Nette\Caching\Cache::PRIORITY]);
        }
        foreach ($keys as $key) {
            $tagManager->deleteTagsForKey($key);
            $priorityManager->unsetPriority($key);
        }
        $this->fileAccessor->writeAndClose($this->journal);
        return $keys;
    }
}
