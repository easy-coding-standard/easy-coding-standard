<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Error\ErrorCollector;

use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer;
use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
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

        /** @var ChangedFilesDetector $changedFilesDetector */
        $changedFilesDetector = $container->get(ChangedFilesDetector::class);
        $changedFilesDetector->clearCache();
    }

    public function test(): void
    {
        $this->runFileProcessor();

        $this->assertSame(3, $this->errorDataCollector->getErrorCount());

        $errorMessages = $this->errorDataCollector->getAllErrors()[self::PROCESSED_FILE];

        /** @var Error $firstError */
        $firstError = $errorMessages[0];
        $this->assertInstanceOf(Error::class, $firstError);
        $this->assertSame(7, $firstError->getLine());
        $this->assertSame(VisibilityRequiredFixer::class, $firstError->getSourceClass());

        /** @var Error $secondError */
        $secondError = $errorMessages[1];
        $this->assertSame(9, $secondError->getLine());
        $this->assertSame(VisibilityRequiredFixer::class, $secondError->getSourceClass());

        /** @var Error $thirdError */
        $thirdError = $errorMessages[2];
        $this->assertSame(11, $thirdError->getLine());
        $this->assertSame(SingleBlankLineAtEofFixer::class, $thirdError->getSourceClass());
    }

    private function runFileProcessor(): void
    {
        $fileInfo = new SplFileInfo(self::PROCESSED_FILE);
        $this->fileProcessor->processFile($fileInfo);
    }
}
