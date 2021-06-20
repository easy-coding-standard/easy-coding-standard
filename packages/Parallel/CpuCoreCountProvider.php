<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Parallel;

/**
 * From https://github.com/phpstan/phpstan-src/commit/9124c66dcc55a222e21b1717ba5f60771f7dda92
 */
final class CpuCoreCountProvider
{
    public function provide() : int
    {
        // from brianium/paratest
        $coreCount = 2;
        if (\is_file('/proc/cpuinfo')) {
            // Linux (and potentially Windows with linux sub systems)
            $cpuinfo = \file_get_contents('/proc/cpuinfo');
            if ($cpuinfo !== \false) {
                \preg_match_all('#^processor#m', $cpuinfo, $matches);
                return \is_array($matches[0]) || $matches[0] instanceof \Countable ? \count($matches[0]) : 0;
            }
        }
        if (\DIRECTORY_SEPARATOR === '\\') {
            // Windows
            $process = @\popen('wmic cpu get NumberOfCores', 'rb');
            if ($process !== \false) {
                \fgets($process);
                $coreCount = (int) \fgets($process);
                \pclose($process);
            }
            return $coreCount;
        }
        $process = @\popen('sysctl -n hw.ncpu', 'rb');
        if ($process !== \false) {
            $coreCount = (int) \fgets($process);
            \pclose($process);
        }
        return $coreCount;
    }
}
