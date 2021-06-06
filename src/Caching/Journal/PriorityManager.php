<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Caching\Journal;

class PriorityManager
{
    /** @var DataContainer */
    private $journal;
    public function __construct(\Symplify\EasyCodingStandard\Caching\Journal\DataContainer $container)
    {
        $this->journal = $container;
    }
    /**
     * @return void
     */
    public function setPriority(string $key, int $priority)
    {
        $this->journal->prioritiesByKey[$key] = $priority;
        if (!isset($this->journal->keysByPriority[$priority])) {
            $this->journal->keysByPriority[$priority] = [$key];
        } else {
            $this->journal->keysByPriority[$priority][] = $key;
        }
    }
    /**
     * @return void
     */
    public function unsetPriority(string $key)
    {
        if (isset($this->journal->prioritiesByKey[$key])) {
            $currentPriority = $this->journal->prioritiesByKey[$key];
            $this->journal->keysByPriority[$currentPriority] = \array_filter($this->journal->keysByPriority[$currentPriority], static function ($itemKey) use($key) {
                return $itemKey !== $key;
            });
        }
        if (isset($this->journal->prioritiesByKey[$key])) {
            unset($this->journal->prioritiesByKey[$key]);
        }
    }
    /**
     * @param int $priorityThreshold
     *
     * @return mixed[]
     */
    public function getKeysByPriority(int $priorityThreshold) : array
    {
        $keys = [];
        foreach (\array_keys($this->journal->keysByPriority) as $priority) {
            if ($priority <= $priorityThreshold) {
                $keys = \array_merge($keys, $this->journal->keysByPriority[$priority] ?? []);
            }
        }
        return $keys;
    }
}
