<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Caching\Journal;

use ECSPrefix20210606\Nette\Utils\Json;
final class DataContainer
{
    /**
     * @var string
     */
    const TAGS = 'tags';
    /**
     * @var string
     */
    const BY_KEY = 'by-key';
    /**
     * @var string
     */
    const PRIORITIES = 'priorities';
    /**
     * @var mixed[]
     */
    public $tagsByKey = [];
    /**
     * @var array[]
     */
    public $keysByTag = [];
    /**
     * @var int[]
     */
    public $prioritiesByKey = [];
    /**
     * @var array[]
     */
    public $keysByPriority = [];
    /**
     * @return $this
     */
    public static function fromJson(string $jsonString)
    {
        $data = \ECSPrefix20210606\Nette\Utils\Json::decode($jsonString, \ECSPrefix20210606\Nette\Utils\Json::FORCE_ARRAY);
        $self = new self();
        $self->tagsByKey = $data[self::TAGS][self::BY_KEY];
        $self->keysByTag = $data[self::TAGS]['by-tag'];
        $self->prioritiesByKey = $data[self::PRIORITIES][self::BY_KEY];
        $self->keysByPriority = $data[self::PRIORITIES]['by-priority'];
        return $self;
    }
    public function toJson() : string
    {
        return \ECSPrefix20210606\Nette\Utils\Json::encode([self::TAGS => [self::BY_KEY => $this->tagsByKey, 'by-tag' => $this->keysByTag], self::PRIORITIES => [self::BY_KEY => $this->prioritiesByKey, 'by-priority' => $this->keysByPriority]]);
    }
}
