<?php

namespace Symplify\EasyCodingStandard\Set;

use Symplify\EasyCodingStandard\ValueObject\Set\SetList;
use ECSPrefix20210517\Symplify\SetConfigResolver\Provider\AbstractSetProvider;
use ECSPrefix20210517\Symplify\SetConfigResolver\ValueObject\Set;
final class EasyCodingStandardSetProvider extends \ECSPrefix20210517\Symplify\SetConfigResolver\Provider\AbstractSetProvider
{
    /**
     * @var Set[]
     */
    private $sets = [];
    public function __construct(\Symplify\EasyCodingStandard\Set\ConstantReflectionSetFactory $constantReflectionSetFactory)
    {
        $this->sets = $constantReflectionSetFactory->createSetsFromClass(\Symplify\EasyCodingStandard\ValueObject\Set\SetList::class);
    }
    /**
     * @return mixed[]
     */
    public function provide()
    {
        return $this->sets;
    }
    /**
     * @return mixed[]
     */
    public function provideSetNames()
    {
        $setNames = [];
        foreach ($this->sets as $set) {
            $setNames[] = $set->getName();
        }
        \sort($setNames);
        return $setNames;
    }
}
