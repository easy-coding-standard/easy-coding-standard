<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Parallel\CommandLine;

final class WorkerCommandLineFactory
{
    /**
     * @var string
     */
    private const OPTION_DASHES = '--';
    /**
     * @param array<string, bool|string|null> $workerOptionValues option name => value, mirrored to the worker process
     * @param string[] $paths
     */
    public function create(string $baseScript, string $workerCommandName, ?string $projectConfigFile, array $workerOptionValues, array $paths, string $identifier, int $port): string
    {
        $processCommandArray = [escapeshellarg(\PHP_BINARY), escapeshellarg($baseScript), $workerCommandName];
        if ($projectConfigFile !== null) {
            $processCommandArray[] = '--config';
            $processCommandArray[] = escapeshellarg($projectConfigFile);
        }
        foreach ($this->mirrorCommandOptions($workerOptionValues) as $processCommandOption) {
            $processCommandArray[] = $processCommandOption;
        }
        // for TCP local server
        $processCommandArray[] = '--port';
        $processCommandArray[] = (string) $port;
        $processCommandArray[] = '--identifier';
        $processCommandArray[] = escapeshellarg($identifier);
        foreach ($paths as $path) {
            $processCommandArray[] = escapeshellarg($path);
        }
        // set json output
        $processCommandArray[] = '--output-format';
        $processCommandArray[] = escapeshellarg('json');
        return implode(' ', $processCommandArray);
    }
    /**
     * @param array<string, bool|string|null> $workerOptionValues
     * @return string[]
     */
    private function mirrorCommandOptions(array $workerOptionValues): array
    {
        $processCommandOptions = [];
        foreach ($workerOptionValues as $optionName => $optionValue) {
            // skip clutter
            if ($optionValue === null) {
                continue;
            }
            if (is_bool($optionValue)) {
                if ($optionValue) {
                    $processCommandOptions[] = self::OPTION_DASHES . $optionName;
                }
                continue;
            }
            if ($optionName === 'memory-limit') {
                // does not accept -1 as value without assign
                $processCommandOptions[] = '--' . $optionName . '=' . $optionValue;
            } else {
                $processCommandOptions[] = self::OPTION_DASHES . $optionName;
                $processCommandOptions[] = escapeshellarg($optionValue);
            }
        }
        return $processCommandOptions;
    }
}
