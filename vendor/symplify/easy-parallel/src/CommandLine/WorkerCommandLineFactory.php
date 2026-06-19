<?php

declare (strict_types=1);
namespace ECSPrefix202606\Symplify\EasyParallel\CommandLine;

/**
 * @api
 * @see \Symplify\EasyParallel\Tests\CommandLine\WorkerCommandLineFactoryTest
 */
final class WorkerCommandLineFactory
{
    /**
     * @var string
     */
    private const OPTION_DASHES = '--';
    /**
     * These options are not relevant for nested worker command line, either
     * because they are handled explicitly below or are framework globals that
     * must not leak into the worker sub-process.
     *
     * @var string[]
     */
    private const EXCLUDED_OPTION_NAMES = ['output-format', 'ansi', 'no-ansi', 'help', 'quiet', 'verbose', 'version', 'no-interaction'];
    /**
     * The worker command name is always the first token, so the sub-process resolves the
     * worker command directly instead of falling back to the application's default command.
     *
     * @param array<string, scalar|null> $optionValues option name => resolved value
     * @param string[] $paths
     */
    public function create(string $baseScript, string $workerCommandName, ?string $projectConfigFile, array $optionValues, array $paths, string $identifier, int $port): string
    {
        $processCommandArray = [escapeshellarg(\PHP_BINARY), escapeshellarg($baseScript), $workerCommandName];
        if ($projectConfigFile !== null) {
            $processCommandArray[] = '--config';
            $processCommandArray[] = escapeshellarg($projectConfigFile);
        }
        $processCommandOptions = $this->mirrorCommandOptions($optionValues);
        $processCommandArray = array_merge($processCommandArray, $processCommandOptions);
        // for TCP local server
        $processCommandArray[] = '--port';
        $processCommandArray[] = $port;
        $processCommandArray[] = '--identifier';
        $processCommandArray[] = escapeshellarg($identifier);
        foreach ($paths as $path) {
            $processCommandArray[] = escapeshellarg($path);
        }
        // set json output
        $processCommandArray[] = '--output-format';
        $processCommandArray[] = escapeshellarg('json');
        // explicitly disable colors, breaks json_decode() otherwise
        // @see https://github.com/symfony/symfony/issues/1238
        $processCommandArray[] = '--no-ansi';
        return implode(' ', $processCommandArray);
    }
    /**
     * Keeps all options that are allowed in check command options
     *
     * @param array<string, scalar|null> $optionValues
     * @return string[]
     */
    private function mirrorCommandOptions(array $optionValues): array
    {
        $processCommandOptions = [];
        foreach ($optionValues as $optionName => $optionValue) {
            if (in_array($optionName, self::EXCLUDED_OPTION_NAMES, \true)) {
                continue;
            }
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
                // symfony/console does not accept -1 as value without assign
                $processCommandOptions[] = '--' . $optionName . '=' . $optionValue;
            } else {
                $processCommandOptions[] = self::OPTION_DASHES . $optionName;
                $processCommandOptions[] = escapeshellarg((string) $optionValue);
            }
        }
        return $processCommandOptions;
    }
}
