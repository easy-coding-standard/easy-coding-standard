<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Error\ErrorCollector;

use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\EasyCodingStandard\Error\Error;
use Symplify\EasyCodingStandard\Error\ErrorCollector;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;

final class FixerLineTest extends TestCase
{
    /**
     * @var string
     */
    private const PROCESSED_FILE = __DIR__ . '/ErrorCollectorSource/ConstantWithoutPublicDeclaration.php.inc';

    /**
     * @var ErrorCollector
     */
    private $errorDataCollector;

    /**
     * @var FixerFileProcessor
     */
    private $fileProcessor;

    protected function setUp(): void
    {
        $container = (new ContainerFactory)->createWithConfig(
            __DIR__ . '/FixerRunnerSource/easy-coding-standard.neon'
        );

        $this->errorDataCollector = $container->get(ErrorCollector::class);
        $this->fileProcessor = $container->get(FixerFileProcessor::class);
    }

    public function test(): void
    {
        $this->runFileProcessor();

        $this->assertSame(2, $this->errorDataCollector->getErrorCount());

        $errorMessages = $this->errorDataCollector->getAllErrors()[self::PROCESSED_FILE];

        /** @var Error $firstError */
        $firstError = $errorMessages[0];
        $this->assertInstanceOf(Error::class, $firstError);
        $this->assertSame(7, $firstError->getLine());

        /** @var Error $secondError */
        $secondError = $errorMessages[1];
        $this->assertInstanceOf(Error::class, $secondError);
        $this->assertSame(9, $secondError->getLine());
    }

    private function runFileProcessor(): void
    {
        $fileInfo = new SplFileInfo(self::PROCESSED_FILE);
        $this->fileProcessor->processFile($fileInfo);
    }
}
