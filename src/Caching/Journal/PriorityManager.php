<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Caching\Journal;

final class PriorityManager
{
    /**
     * @var DataContainer
     */
    private $dataContainer;
    public function __construct(\Symplify\EasyCodingStandard\Caching\Journal\DataContainer $dataContainer)
    {
        $this->dataContainer = $dataContainer;
    }
    /**
     * @return void
     */
    public function setPriority(string $key, int $priority)
    {
        $this->dataContainer->prioritiesByKey[$key] = $priority;
        if (!isset($this->dataContainer->keysByPriority[$priority])) {
            $this->dataContainer->keysByPriority[$priority] = [$key];
        } else {
            $this->dataContainer->keysByPriority[$priority][] = $key;
        }
    }
    /**
     * @return void
     */
    public function unsetPriority(string $key)
    {
        if (isset($this->dataContainer->prioritiesByKey[$key])) {
            $currentPriority = $this->dataContainer->prioritiesByKey[$key];
            $this->dataContainer->keysByPriority[$currentPriority] = \array_filter($this->dataContainer->keysByPriority[$currentPriority], static function ($itemKey) use($key) : bool {
                return $itemKey !== $key;
            });
        }
        if (isset($this->dataContainer->prioritiesByKey[$key])) {
            unset($this->dataContainer->prioritiesByKey[$key]);
        }
    }
    /**
     * @return mixed[]
     */
    public function getKeysByPriority(int $priorityThreshold) : array
    {
        $keys = [];
        foreach (\array_keys($this->dataContainer->keysByPriority) as $priority) {
            if ($priority <= $priorityThreshold) {
                $keys = \array_merge($keys, $this->dataContainer->keysByPriority[$priority] ?? []);
            }
        }
        return $keys;
    }
}
