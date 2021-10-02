<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Parallel\Command;

use ECSPrefix20211002\Symfony\Component\Console\Input\InputInterface;
use Symplify\EasyCodingStandard\Console\Command\CheckCommand;
use Symplify\EasyCodingStandard\Console\Command\WorkerCommand;
use Symplify\EasyCodingStandard\Console\Output\JsonOutputFormatter;
use Symplify\EasyCodingStandard\ValueObject\Option;
use ECSPrefix20211002\Symplify\PackageBuilder\Console\Command\CommandNaming;
/**
 * @see \Symplify\EasyCodingStandard\Tests\Parallel\Command\WorkerCommandLineFactoryTest
 */
final class WorkerCommandLineFactory
{
    /**
     * @var string
     */
    private const _ = '--';
    /**
     * @var \Symplify\EasyCodingStandard\Console\Command\CheckCommand
     */
    private $checkCommand;
    public function __construct(\Symplify\EasyCodingStandard\Console\Command\CheckCommand $checkCommand)
    {
        $this->checkCommand = $checkCommand;
    }
    public function create(string $mainScript, ?string $projectConfigFile, \ECSPrefix20211002\Symfony\Component\Console\Input\InputInterface $input, string $identifier, int $port) : string
    {
        $args = \array_merge([\PHP_BINARY, $mainScript], \array_slice($_SERVER['argv'], 1));
        $processCommandArray = [];
        foreach ($args as $arg) {
            if ($arg === \ECSPrefix20211002\Symplify\PackageBuilder\Console\Command\CommandNaming::classToName(\Symplify\EasyCodingStandard\Console\Command\CheckCommand::class)) {
                break;
            }
            $processCommandArray[] = \escapeshellarg($arg);
        }
        $processCommandArray[] = \ECSPrefix20211002\Symplify\PackageBuilder\Console\Command\CommandNaming::classToName(\Symplify\EasyCodingStandard\Console\Command\WorkerCommand::class);
        if ($projectConfigFile !== null) {
            $processCommandArray[] = self::_ . \Symplify\EasyCodingStandard\ValueObject\Option::CONFIG;
            $processCommandArray[] = \escapeshellarg($projectConfigFile);
        }
        $checkCommandOptionNames = $this->getCheckCommandOptionNames();
        foreach ($checkCommandOptionNames as $checkCommandOptionName) {
            if (!$input->hasOption($checkCommandOptionName)) {
                continue;
            }
            // skip output format
            if ($checkCommandOptionName === \Symplify\EasyCodingStandard\ValueObject\Option::OUTPUT_FORMAT) {
                continue;
            }
            /** @var bool|string|null $optionValue */
            $optionValue = $input->getOption($checkCommandOptionName);
            if (\is_bool($optionValue)) {
                if ($optionValue) {
                    $processCommandArray[] = \sprintf('--%s', $checkCommandOptionName);
                }
                continue;
            }
            if ($optionValue === null) {
                continue;
            }
            $processCommandArray[] = self::_ . $checkCommandOptionName;
            $processCommandArray[] = \escapeshellarg($optionValue);
        }
        // for TCP local server
        $processCommandArray[] = '--port';
        $processCommandArray[] = $port;
        $processCommandArray[] = '--identifier';
        $processCommandArray[] = \escapeshellarg($identifier);
        /** @var string[] $paths */
        $paths = $input->getArgument(\Symplify\EasyCodingStandard\ValueObject\Option::PATHS);
        foreach ($paths as $path) {
            $processCommandArray[] = \escapeshellarg($path);
        }
        // set json output
        $processCommandArray[] = self::_ . \Symplify\EasyCodingStandard\ValueObject\Option::OUTPUT_FORMAT;
        $processCommandArray[] = \escapeshellarg(\Symplify\EasyCodingStandard\Console\Output\JsonOutputFormatter::NAME);
        // disable colors, breaks json_decode() otherwise
        // @see https://github.com/symfony/symfony/issues/1238
        $processCommandArray[] = '--no-ansi';
        return \implode(' ', $processCommandArray);
    }
    /**
     * @return string[]
     */
    private function getCheckCommandOptionNames() : array
    {
        $inputDefinition = $this->checkCommand->getDefinition();
        $optionNames = [];
        foreach ($inputDefinition->getOptions() as $inputOption) {
            $optionNames[] = $inputOption->getName();
        }
        return $optionNames;
    }
}
