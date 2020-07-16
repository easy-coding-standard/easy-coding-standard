<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Skipper\Only;

use Iterator;
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\Skipper;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class SkipperOnlyTest extends AbstractKernelTestCase
{
    /**
     * @var Skipper
     */
    private $skipper;

    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(EasyCodingStandardKernel::class, [__DIR__ . '/config.yaml']);

        $this->skipper = self::$container->get(Skipper::class);
    }

    /**
     * @dataProvider provideCheckerAndFile()
     */
    public function testCheckerAndFile(string $checker, string $filePath, bool $expected): void
    {
        $this->assertSame($expected, $this->skipper->shouldSkipCheckerAndFile($checker, new SmartFileInfo($filePath)));
    }

    public function provideCheckerAndFile(): Iterator
    {
        yield [LineLengthFixer::class, __DIR__ . '/Source/SomeFileToOnlyInclude.php', false];
        yield [LineLengthFixer::class, __DIR__ . '/Source/SomeFile.php', true];

        // no restrictions
        yield [ArraySyntaxFixer::class, __DIR__ . '/Source/SomeFileToOnlyInclude.php', false];
        yield [ArraySyntaxFixer::class, __DIR__ . '/Source/SomeFile.php', false];
    }
}
