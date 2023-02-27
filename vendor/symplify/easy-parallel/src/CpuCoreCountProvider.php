<?php

declare (strict_types=1);
namespace ECSPrefix202302\Symplify\EasyParallel;

use ECSPrefix202302\Fidry\CpuCoreCounter\CpuCoreCounter;
use ECSPrefix202302\Fidry\CpuCoreCounter\NumberOfCpuCoreNotFound;
/**
 * @api
 */
final class CpuCoreCountProvider
{
    /**
     * @var int
     */
    private const DEFAULT_CORE_COUNT = 2;
    public function provide() : int
    {
        try {
            return (new CpuCoreCounter())->getCount();
        } catch (NumberOfCpuCoreNotFound $exception) {
            return self::DEFAULT_CORE_COUNT;
        }
    }
}
