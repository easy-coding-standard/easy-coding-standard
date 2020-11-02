<?php

declare(strict_types=1);

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

    public function __construct(ConstantReflectionSetFactory $constantReflectionSetFactory)
    {
        $this->sets = $constantReflectionSetFactory->createSetsFromClass(SetList::class);
    }

    /**
     * @return Set[]
     */
    public function provide(): array
    {
        return $this->sets;
    }

    /**
     * @return string[]
     */
    public function provideSetNames(): array
    {
        $setNames = [];
        foreach ($this->sets as $set) {
            $setNames[] = $set->getName();
        }

        sort($setNames);

        return $setNames;
    }
}
