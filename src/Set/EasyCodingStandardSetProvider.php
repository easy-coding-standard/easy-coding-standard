<?php

namespace Symplify\EasyCodingStandard\Set;

use Symplify\EasyCodingStandard\ValueObject\Set\SetList;
use Symplify\SetConfigResolver\Provider\AbstractSetProvider;
use Symplify\SetConfigResolver\ValueObject\Set;
final class EasyCodingStandardSetProvider extends AbstractSetProvider
{
    /**
     * @var Set[]
     */
    private $sets = [];
    /**
     * @param \Symplify\EasyCodingStandard\Set\ConstantReflectionSetFactory $constantReflectionSetFactory
     */
    public function __construct($constantReflectionSetFactory)
    {
        $this->sets = $constantReflectionSetFactory->createSetsFromClass(SetList::class);
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
