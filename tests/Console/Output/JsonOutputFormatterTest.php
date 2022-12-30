<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Console\Output;

use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\EasyCodingStandard\Console\Output\JsonOutputFormatter;
use Symplify\EasyCodingStandard\Kernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use Symplify\PackageBuilder\Console\Formatter\ColorConsoleDiffFormatter;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class JsonOutputFormatterTest extends AbstractKernelTestCase
{
    private JsonOutputFormatter $jsonOutputFormatter;

    private ColorConsoleDiffFormatter $colorConsoleDiffFormatter;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCodingStandardKernel::class);

        $this->jsonOutputFormatter = $this->getService(JsonOutputFormatter::class);
        $this->colorConsoleDiffFormatter = $this->getService(ColorConsoleDiffFormatter::class);
    }

    public function test(): void
    {
        $randomFileInfo = new SmartFileInfo(__DIR__ . '/Source/RandomFile.php');

        $fileDiffs = [];

        $diff = 'some diff';
        $fileDiffs[] = new FileDiff(
            $randomFileInfo->getRelativeFilePathFromCwd(),
            $diff,
            $this->colorConsoleDiffFormatter->format($diff),
            [LineLengthFixer::class]
        );

        $diff = 'some other diff';
        $fileDiffs[] = new FileDiff(
            $randomFileInfo->getRelativeFilePathFromCwd(),
            $diff,
            $this->colorConsoleDiffFormatter->format($diff),
            [LineLengthFixer::class]
        );

        $errorAndDiffResult = new ErrorAndDiffResult([], $fileDiffs, []);

        $jsonContent = $this->jsonOutputFormatter->createJsonContent($errorAndDiffResult);
        $this->assertStringMatchesFormatFile(__DIR__ . '/Fixture/expected_json_output.json', $jsonContent . PHP_EOL);
    }
}
