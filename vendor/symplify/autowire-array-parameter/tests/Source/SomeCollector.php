<?php

declare(strict_types=1);

namespace Symplify\AutowireArrayParameter\Tests\Source;

use Symplify\AutowireArrayParameter\Tests\Source\Contract\FirstCollectedInterface;
use Symplify\AutowireArrayParameter\Tests\Source\Contract\SecondCollectedInterface;

final class SomeCollector
{
    /**
     * @var FirstCollectedInterface[]
     */
    private $firstCollected = [];

    /**
     * @var SecondCollectedInterface[]
     */
    private $secondCollected = [];

    /**
     * @param FirstCollectedInterface[] $firstCollected
     * @param SecondCollectedInterface[] $secondCollected
     */
    public function __construct(array $firstCollected, array $secondCollected)
    {
        $this->firstCollected = $firstCollected;
        $this->secondCollected = $secondCollected;
    }

    /**
     * @return FirstCollectedInterface[]
     */
    public function getFirstCollected(): array
    {
        return $this->firstCollected;
    }

    /**
     * @return SecondCollectedInterface[]
     */
    public function getSecondCollected(): array
    {
        return $this->secondCollected;
    }
}
