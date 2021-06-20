<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Parallel;

use Symplify\EasyCodingStandard\Parallel\ValueObject\Schedule;
/**
 * Used from
 * https://github.com/phpstan/phpstan-src/blob/9124c66dcc55a222e21b1717ba5f60771f7dda92/src/Parallel/Scheduler.php
 */
final class ScheduleFactory
{
    /**
     * @param array<string> $files
     */
    public function create(int $cpuCores, int $jobSize, array $files) : \Symplify\EasyCodingStandard\Parallel\ValueObject\Schedule
    {
        $jobs = \array_chunk($files, $jobSize);
        $numberOfProcesses = \min(\count($jobs), $cpuCores);
        return new \Symplify\EasyCodingStandard\Parallel\ValueObject\Schedule($numberOfProcesses, $jobs);
    }
}
