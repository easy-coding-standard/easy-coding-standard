<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Parallel;

use Symplify\EasyCodingStandard\Parallel\ValueObject\Schedule;
final class ScheduleFactory
{
    /**
     * @param array<string> $files
     */
    public function scheduleWork(int $cpuCores, int $jobSize, array $files) : \Symplify\EasyCodingStandard\Parallel\ValueObject\Schedule
    {
        $jobs = \array_chunk($files, $jobSize);
        $numberOfProcesses = \min(\count($jobs), $cpuCores);
        return new \Symplify\EasyCodingStandard\Parallel\ValueObject\Schedule($numberOfProcesses, $jobs);
    }
}
