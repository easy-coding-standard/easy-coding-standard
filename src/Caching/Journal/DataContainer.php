<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Caching\Journal;

use ECSPrefix20210606\Nette\Utils\Json;
class DataContainer
{
    /** @var mixed[] */
    public $tagsByKey = [];
    /** @var array[] */
    public $keysByTag = [];
    /** @var int[] */
    public $prioritiesByKey = [];
    /** @var array[] */
    public $keysByPriority = [];
    /**
     * @return $this
     */
    public static function fromJson(string $jsonString)
    {
        $data = \ECSPrefix20210606\Nette\Utils\Json::decode($jsonString, \ECSPrefix20210606\Nette\Utils\Json::FORCE_ARRAY);
        $instance = new self();
        $instance->tagsByKey = $data['tags']['by-key'];
        $instance->keysByTag = $data['tags']['by-tag'];
        $instance->prioritiesByKey = $data['priorities']['by-key'];
        $instance->keysByPriority = $data['priorities']['by-priority'];
        return $instance;
    }
    public function toJson() : string
    {
        return \ECSPrefix20210606\Nette\Utils\Json::encode(['tags' => ['by-key' => $this->tagsByKey, 'by-tag' => $this->keysByTag], 'priorities' => ['by-key' => $this->prioritiesByKey, 'by-priority' => $this->keysByPriority]]);
    }
}
