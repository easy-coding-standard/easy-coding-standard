<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Parallel;

use ECSPrefix202608\Fidry\CpuCoreCounter\CpuCoreCounter;
use ECSPrefix202608\Fidry\CpuCoreCounter\NumberOfCpuCoreNotFound;
final class CpuCoreCountProvider
{
    /**
     * @var int
     */
    private const DEFAULT_CORE_COUNT = 2;
    public function provide(): int
    {
        try {
            return (new CpuCoreCounter())->getCount();
        } catch (NumberOfCpuCoreNotFound $exception) {
            return self::DEFAULT_CORE_COUNT;
        }
    }
}
