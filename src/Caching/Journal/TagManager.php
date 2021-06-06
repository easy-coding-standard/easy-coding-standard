<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Caching\Journal;

class TagManager
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
    public function deleteTagsForKey(string $key)
    {
        if (isset($this->journal->tagsByKey[$key])) {
            $currentTags = $this->journal->tagsByKey[$key];
            unset($this->journal->tagsByKey[$key]);
        } else {
            $currentTags = [];
        }
        foreach ($currentTags as $tag) {
            if (isset($this->journal->keysByTag[$tag])) {
                $this->journal->keysByTag[$tag] = \array_filter($this->journal->keysByTag[$tag], static function ($itemKey) use($key) {
                    return $itemKey !== $key;
                });
            }
        }
    }
    /**
     * @return void
     */
    public function addTagsForKey(string $key, array $tags)
    {
        $this->journal->tagsByKey[$key] = $tags;
        foreach ($tags as $tag) {
            if (!isset($this->journal->keysByTag[$tag])) {
                $this->journal->keysByTag[$tag] = [];
            }
            $this->journal->keysByTag[$tag][] = $key;
        }
    }
    /**
     * @param array $tags
     *
     * @return mixed[]
     */
    public function getKeysByTags($tags) : array
    {
        $keys = [];
        foreach ($tags as $tag) {
            $keys = \array_merge($keys, $this->journal->keysByTag[$tag] ?? []);
        }
        return $keys;
    }
}
