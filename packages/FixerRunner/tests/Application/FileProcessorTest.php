<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\Tests\Application;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Application\Command\RunCommand;
use Symplify\EasyCodingStandard\FixerRunner\Application\FileProcessor;
use Symplify\EasyCodingStandard\Tests\ContainerFactoryWithCustomConfig;

final class FileProcessorTest extends TestCase
{
    /**
     * @var FileProcessor
     */
    private $fileProcessor;

    protected function setUp(): void
    {
        $container = (new ContainerFactoryWithCustomConfig)->createWithConfig(
            __DIR__ . '/FileProcessorSource/easy-coding-standard.neon'
        );

        $this->fileProcessor = $container->getByType(FileProcessor::class);
    }

    public function test(): void
    {
        $runCommand = $this->createRunCommand();
        $this->fileProcessor->setupWithCommand($runCommand);

        $this->assertCount(1, Assert::getObjectAttribute($this->fileProcessor, 'fixers'));
    }

    private function createRunCommand(): RunCommand
    {
        return RunCommand::createForSourceFixerAndClearCache([__DIR__], false, true);
    }
}
