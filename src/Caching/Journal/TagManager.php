<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Caching\Journal;

final class TagManager
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
    public function deleteTagsForKey(string $key)
    {
        if (isset($this->dataContainer->tagsByKey[$key])) {
            $currentTags = $this->dataContainer->tagsByKey[$key];
            unset($this->dataContainer->tagsByKey[$key]);
        } else {
            $currentTags = [];
        }
        foreach ($currentTags as $tag) {
            if (isset($this->dataContainer->keysByTag[$tag])) {
                $this->dataContainer->keysByTag[$tag] = \array_filter($this->dataContainer->keysByTag[$tag], static function ($itemKey) use($key) : bool {
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
        $this->dataContainer->tagsByKey[$key] = $tags;
        foreach ($tags as $tag) {
            if (!isset($this->dataContainer->keysByTag[$tag])) {
                $this->dataContainer->keysByTag[$tag] = [];
            }
            $this->dataContainer->keysByTag[$tag][] = $key;
        }
    }
    /**
     * @return mixed[]
     */
    public function getKeysByTags(array $tags) : array
    {
        $keys = [];
        foreach ($tags as $tag) {
            $keys = \array_merge($keys, $this->dataContainer->keysByTag[$tag] ?? []);
        }
        return $keys;
    }
}
