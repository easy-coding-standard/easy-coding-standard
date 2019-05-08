<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Skipper\Only;

use Iterator;
use Symplify\CodingStandard\Fixer\Solid\FinalInterfaceFixer;
use Symplify\CodingStandard\Sniffs\Naming\AbstractClassNameSniff;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\Skipper;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

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
        yield [AbstractClassNameSniff::class, __DIR__ . '/Source/SomeFileToOnlyInclude.php', false];
        yield [AbstractClassNameSniff::class, __DIR__ . '/Source/SomeFile.php', true];

        // no restrictions
        yield [FinalInterfaceFixer::class, __DIR__ . '/Source/SomeFileToOnlyInclude.php', false];
        yield [FinalInterfaceFixer::class, __DIR__ . '/Source/SomeFile.php', false];
    }
}
