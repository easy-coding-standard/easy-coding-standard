<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Console\Output;

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\EasyCodingStandard\Console\Output\JsonOutputFormatter;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffResultFactory;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class JsonOutputFormatterTest extends AbstractKernelTestCase
{
    /**
     * @var JsonOutputFormatter
     */
    private $jsonOutputFormatter;

    /**
     * @var ErrorAndDiffCollector
     */
    private $errorAndDiffCollector;

    /**
     * @var ErrorAndDiffResultFactory
     */
    private $errorAndDiffResultFactory;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCodingStandardKernel::class);

        $this->jsonOutputFormatter = self::$container->get(JsonOutputFormatter::class);
        $this->errorAndDiffCollector = self::$container->get(ErrorAndDiffCollector::class);
        $this->errorAndDiffResultFactory = self::$container->get(ErrorAndDiffResultFactory::class);
    }

    public function test(): void
    {
        $randomFileInfo = new SmartFileInfo(__DIR__ . '/Source/RandomFile.php');
        $this->errorAndDiffCollector->addErrorMessage($randomFileInfo, 100, 'Error message', ArraySyntaxFixer::class);

        $this->errorAndDiffCollector->addDiffForFileInfo($randomFileInfo, 'some diff', [LineLengthFixer::class]);
        $this->errorAndDiffCollector->addDiffForFileInfo($randomFileInfo, 'some other diff', [LineLengthFixer::class]);

        $errorAndDiffResult = $this->errorAndDiffResultFactory->create($this->errorAndDiffCollector);

        $jsonContent = $this->jsonOutputFormatter->createJsonContent($errorAndDiffResult);
        $this->assertStringMatchesFormatFile(__DIR__ . '/Fixture/expected_json_output.json', $jsonContent . PHP_EOL);
    }
}
