<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Parallel\Command;

use ECSPrefix20210619\Symfony\Component\Console\Input\InputInterface;
use Symplify\EasyCodingStandard\Console\Command\CheckCommand;
use Symplify\EasyCodingStandard\Console\Command\WorkerCommand;
use Symplify\EasyCodingStandard\ValueObject\Option;
use ECSPrefix20210619\Symplify\PackageBuilder\Console\Command\CommandNaming;
/**
 * @see \Symplify\EasyCodingStandard\Tests\Parallel\Command\WorkerCommandLineFactoryTest
 */
final class WorkerCommandLineFactory
{
    /**
     * @var \Symplify\EasyCodingStandard\Console\Command\CheckCommand
     */
    private $checkCommand;
    public function __construct(\Symplify\EasyCodingStandard\Console\Command\CheckCommand $checkCommand)
    {
        $this->checkCommand = $checkCommand;
    }
    /**
     * @param string|null $projectConfigFile
     */
    public function create(string $mainScript, $projectConfigFile, \ECSPrefix20210619\Symfony\Component\Console\Input\InputInterface $input) : string
    {
        $args = \array_merge([\PHP_BINARY, $mainScript], \array_slice($_SERVER['argv'], 1));
        $processCommandArray = [];
        foreach ($args as $arg) {
            if ($arg === \ECSPrefix20210619\Symplify\PackageBuilder\Console\Command\CommandNaming::classToName(\Symplify\EasyCodingStandard\Console\Command\CheckCommand::class)) {
                break;
            }
            $processCommandArray[] = \escapeshellarg($arg);
        }
        $processCommandArray[] = \ECSPrefix20210619\Symplify\PackageBuilder\Console\Command\CommandNaming::classToName(\Symplify\EasyCodingStandard\Console\Command\WorkerCommand::class);
        if ($projectConfigFile !== null) {
            $processCommandArray[] = '--' . \Symplify\EasyCodingStandard\ValueObject\Option::CONFIG;
            $processCommandArray[] = \escapeshellarg($projectConfigFile);
        }
        $checkCommandOptionNames = $this->getCheckCommandOptionNames();
        foreach ($checkCommandOptionNames as $checkCommandOptionName) {
            if (!$input->hasOption($checkCommandOptionName)) {
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
            $processCommandArray[] = \sprintf('--%s', $checkCommandOptionName);
            $processCommandArray[] = \escapeshellarg($optionValue);
        }
        /** @var string[] $paths */
        $paths = $input->getArgument(\Symplify\EasyCodingStandard\ValueObject\Option::PATHS);
        foreach ($paths as $path) {
            $processCommandArray[] = \escapeshellarg($path);
        }
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
