<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Console\Command;

use ECSPrefix202606\Entropy\Console\Contract\CommandInterface;
use ECSPrefix202606\Entropy\Console\Contract\DefaultCommandInterface;
use Symplify\EasyCodingStandard\Application\EasyCodingStandardApplication;
use Symplify\EasyCodingStandard\Configuration\ConfigInitializer;
use Symplify\EasyCodingStandard\Configuration\ConfigurationFactory;
use Symplify\EasyCodingStandard\Console\ExitCode;
use Symplify\EasyCodingStandard\Console\Output\ConsoleOutputFormatter;
use Symplify\EasyCodingStandard\MemoryLimitter;
use Symplify\EasyCodingStandard\Reporter\ProcessedFileReporter;
final class CheckCommand implements CommandInterface, DefaultCommandInterface
{
    /**
     * @readonly
     * @var \Symplify\EasyCodingStandard\Reporter\ProcessedFileReporter
     */
    private $processedFileReporter;
    /**
     * @readonly
     * @var \Symplify\EasyCodingStandard\MemoryLimitter
     */
    private $memoryLimitter;
    /**
     * @readonly
     * @var \Symplify\EasyCodingStandard\Configuration\ConfigInitializer
     */
    private $configInitializer;
    /**
     * @readonly
     * @var \Symplify\EasyCodingStandard\Application\EasyCodingStandardApplication
     */
    private $easyCodingStandardApplication;
    /**
     * @readonly
     * @var \Symplify\EasyCodingStandard\Configuration\ConfigurationFactory
     */
    private $configurationFactory;
    public function __construct(ProcessedFileReporter $processedFileReporter, MemoryLimitter $memoryLimitter, ConfigInitializer $configInitializer, EasyCodingStandardApplication $easyCodingStandardApplication, ConfigurationFactory $configurationFactory)
    {
        $this->processedFileReporter = $processedFileReporter;
        $this->memoryLimitter = $memoryLimitter;
        $this->configInitializer = $configInitializer;
        $this->easyCodingStandardApplication = $easyCodingStandardApplication;
        $this->configurationFactory = $configurationFactory;
    }
    public function getName(): string
    {
        return 'check';
    }
    public function getDescription(): string
    {
        return 'Check coding standard in one or more directories';
    }
    /**
     * @param string $config       Path to config file
     * @param string $outputFormat Select output format
     * @param string $memoryLimit  Memory limit for check
     * @param string $port         [INTERNAL] parallel TCP port
     * @param string $identifier   [INTERNAL] parallel identifier
     * @param string ...$paths     The path(s) to be checked.
     *
     * @option $config
     * @option $outputFormat
     * @option $memoryLimit
     * @option $port
     * @option $identifier
     *
     * @api invoked via reflection by the Entropy console application
     *
     * @return ExitCode::*
     */
    public function run(bool $fix = \false, bool $clearCache = \false, bool $noProgressBar = \false, bool $noErrorTable = \false, bool $noDiffs = \false, bool $debug = \false, string $config = '', string $outputFormat = ConsoleOutputFormatter::NAME, string $memoryLimit = '', string $port = '', string $identifier = '', string ...$paths): int
    {
        // create ecs.php config file if does not exist yet
        if (!$this->configInitializer->areSomeCheckersRegistered()) {
            $this->configInitializer->createConfig((string) getcwd());
            return ExitCode::SUCCESS;
        }
        $configuration = $this->configurationFactory->create(array_values($paths), $fix, $clearCache, $noProgressBar, $noErrorTable, $noDiffs, $outputFormat, $config !== '' ? $config : null, $port, $identifier, $memoryLimit !== '' ? $memoryLimit : null, $debug);
        $this->memoryLimitter->adjust($configuration);
        $errorsAndDiffs = $this->easyCodingStandardApplication->run($configuration);
        return $this->processedFileReporter->report($errorsAndDiffs, $configuration);
    }
}
