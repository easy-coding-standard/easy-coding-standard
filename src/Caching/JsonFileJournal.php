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
final class JsonFileJournal implements \ECSPrefix20210606\Nette\Caching\Storages\Journal
{
    /**
     * @var LockingJsonFileAccessor
     */
    private $lockingJsonFileAccessor;
    /**
     * @var DataContainer
     */
    private $dataContainer;
    public function __construct(string $journalFilePath = 'journal.json')
    {
        $this->lockingJsonFileAccessor = new \Symplify\EasyCodingStandard\Caching\JsonFile\LockingJsonFileAccessor($journalFilePath);
        if ($this->lockingJsonFileAccessor->exists() && !$this->lockingJsonFileAccessor->isWritable()) {
            throw new \ECSPrefix20210606\Symfony\Component\Filesystem\Exception\IOException(\sprintf("Cache journal file '%s' is not writable", $journalFilePath));
        }
        if (!$this->lockingJsonFileAccessor->exists()) {
            $smartFileSystem = new \ECSPrefix20210606\Symplify\SmartFileSystem\SmartFileSystem();
            $dataContainer = new \Symplify\EasyCodingStandard\Caching\Journal\DataContainer();
            $smartFileSystem->dumpFile($journalFilePath, $dataContainer->toJson());
        }
    }
    /**
     * @return void
     */
    public function write(string $key, array $dependencies)
    {
        $this->dataContainer = $this->lockingJsonFileAccessor->openAndRead();
        if (isset($dependencies[\ECSPrefix20210606\Nette\Caching\Cache::TAGS]) && (\is_array($dependencies[\ECSPrefix20210606\Nette\Caching\Cache::TAGS]) || $dependencies[\ECSPrefix20210606\Nette\Caching\Cache::TAGS] instanceof \Countable ? \count($dependencies[\ECSPrefix20210606\Nette\Caching\Cache::TAGS]) : 0) > 0) {
            $tagManager = new \Symplify\EasyCodingStandard\Caching\Journal\TagManager($this->dataContainer);
            $tagManager->deleteTagsForKey($key);
            $tagManager->addTagsForKey($key, $dependencies[\ECSPrefix20210606\Nette\Caching\Cache::TAGS]);
        }
        if (isset($dependencies[\ECSPrefix20210606\Nette\Caching\Cache::PRIORITY])) {
            $priorityManager = new \Symplify\EasyCodingStandard\Caching\Journal\PriorityManager($this->dataContainer);
            $priorityManager->unsetPriority($key);
            $priorityManager->setPriority($key, (int) $dependencies[\ECSPrefix20210606\Nette\Caching\Cache::PRIORITY]);
        }
        $this->lockingJsonFileAccessor->writeAndClose($this->dataContainer);
    }
    /**
     * @param array<string, mixed> $conditions
     * @return mixed[]|null
     */
    public function clean(array $conditions)
    {
        $this->dataContainer = $this->lockingJsonFileAccessor->openAndRead();
        if (isset($conditions[\ECSPrefix20210606\Nette\Caching\Cache::ALL])) {
            $this->lockingJsonFileAccessor->writeAndClose(new \Symplify\EasyCodingStandard\Caching\Journal\DataContainer());
            return null;
        }
        $tagManager = new \Symplify\EasyCodingStandard\Caching\Journal\TagManager($this->dataContainer);
        $priorityManager = new \Symplify\EasyCodingStandard\Caching\Journal\PriorityManager($this->dataContainer);
        $keys = [];
        if (isset($conditions[\ECSPrefix20210606\Nette\Caching\Cache::TAGS]) && (\is_array($conditions[\ECSPrefix20210606\Nette\Caching\Cache::TAGS]) || $conditions[\ECSPrefix20210606\Nette\Caching\Cache::TAGS] instanceof \Countable ? \count($conditions[\ECSPrefix20210606\Nette\Caching\Cache::TAGS]) : 0) > 0) {
            $keys += $tagManager->getKeysByTags($conditions[\ECSPrefix20210606\Nette\Caching\Cache::TAGS]);
        }
        foreach ($keys as $key) {
            $tagManager->deleteTagsForKey($key);
            $priorityManager->unsetPriority($key);
        }
        $this->lockingJsonFileAccessor->writeAndClose($this->dataContainer);
        return $keys;
    }
}
