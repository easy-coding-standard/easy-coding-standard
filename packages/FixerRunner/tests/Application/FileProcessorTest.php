<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\Tests\Application;

use Nette\DI\Config\Loader;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Application\Command\RunCommand;
use Symplify\EasyCodingStandard\FixerRunner\Application\FileProcessor;
use Symplify\PackageBuilder\Adapter\Nette\GeneralContainerFactory;

final class FileProcessorTest extends TestCase
{
    /**
     * @var FileProcessor
     */
    private $fileProcessor;

    /**
     * @var Loader
     */
    private $configLoader;

    protected function setUp(): void
    {
        $container = (new GeneralContainerFactory)->createFromConfig(
            __DIR__ . '/../../../../src/config/config.neon'
        );
        $this->fileProcessor = $container->getByType(FileProcessor::class);
        $this->configLoader = $container->getByType(Loader::class);
    }

    public function test(): void
    {
        $runCommand = $this->createRunCommand();
        $this->fileProcessor->setupWithCommand($runCommand);

        $this->assertGreaterThan(50, Assert::getObjectAttribute($this->fileProcessor, 'fixers'));
    }

    private function createRunCommand(): RunCommand
    {
        $configurationData = $this->configLoader->load(
            __DIR__ . '/../../../../config/php-cs-fixer/symfony-fixers.neon'
        );

        return RunCommand::createFromSourceFixerAndData([__DIR__], false, true, $configurationData);
    }
}
