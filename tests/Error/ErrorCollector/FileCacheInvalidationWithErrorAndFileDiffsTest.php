<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Error\ErrorCollector;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\FileSystem\FileFilter;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;

final class FileCacheInvalidationWithErrorAndFileDiffsTest extends TestCase
{
    /**
     * @var ErrorAndDiffCollector
     */
    private $errorAndDiffCollector;

    /**
     * @var FixerFileProcessor
     */
    private $fixerFileProcessor;

    /**
     * @var FileFilter
     */
    private $fileFilter;

    protected function setUp(): void
    {
        $container = (new ContainerFactory())->createWithConfig(
            __DIR__ . '/FixerRunnerSource/phpunit-fixer-config.yml'
        );

        $this->errorAndDiffCollector = $container->get(ErrorAndDiffCollector::class);
        $this->fixerFileProcessor = $container->get(FixerFileProcessor::class);
        $this->fileFilter = $container->get(FileFilter::class);
    }

    public function test(): void
    {
        // key as relative path - @todo move to factory and set relative path to ltrim(getcwd(), $absolutePath);
        $files['packages/EasyCodingStandard/tests/Error/ErrorCollector/ErrorCollectorSource/NotPsr2Class.php.inc'] = new SplFileInfo(
            __DIR__ . '/ErrorCollectorSource/NotPsr2Class.php.inc', '', ''
        );
        $this->assertCount(1, $this->fileFilter->filterOnlyChangedFiles($files));

        $this->runFileProcessor();

        $this->assertSame(0, $this->errorAndDiffCollector->getErrorCount());
        $this->assertSame(1, $this->errorAndDiffCollector->getFileDiffsCount());

        $this->assertCount(1, $this->fileFilter->filterOnlyChangedFiles($files));

        $this->errorAndDiffCollector->resetCounters();

        $this->assertSame(0, $this->errorAndDiffCollector->getErrorCount());
        $this->assertSame(1, $this->errorAndDiffCollector->getFileDiffsCount());
    }

    private function runFileProcessor(): void
    {
        $fileInfo = new SplFileInfo(__DIR__ . '/ErrorCollectorSource/NotPsr2Class.php.inc', '', '');

        $this->fixerFileProcessor->processFile($fileInfo);
    }
}
