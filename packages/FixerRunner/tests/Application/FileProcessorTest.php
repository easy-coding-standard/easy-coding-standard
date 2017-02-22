<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\Tests\Application;

use Nette\Neon\Neon;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\FixerRunner\Application\FileProcessor;
use Symplify\PackageBuilder\Adapter\Nette\GeneralContainerFactory;

final class FileProcessorTest extends TestCase
{
    /**
     * @var FileProcessor
     */
    private $fileProcessor;

    protected function setUp(): void
    {
        $container = (new GeneralContainerFactory)->createFromConfig(
            __DIR__ . '/../../../../src/config/config.neon'
        );
        $this->fileProcessor = $container->getByType(FileProcessor::class);
    }

    public function test(): void
    {
        $symfonyFixersFile = file_get_contents(__DIR__ . '/../../../../config/php-cs-fixer/symfony-fixers.neon');

        $symfonyFixersNeon = Neon::decode($symfonyFixersFile);
        $fixerClasses = $symfonyFixersNeon['php-cs-fixer']['fixers'];
        $this->fileProcessor->registerFixers($fixerClasses);

        $this->assertCount(70, Assert::getObjectAttribute($this->fileProcessor, 'fixers'));
    }
}
