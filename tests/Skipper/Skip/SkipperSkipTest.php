<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Skipper\Skip;

use Iterator;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use Symplify\CodingStandard\Fixer\Solid\FinalInterfaceFixer;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\Skipper;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class SkipperSkipTest extends AbstractKernelTestCase
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
        yield [DeclareStrictTypesFixer::class, __DIR__ . '/Source/someFile', true];
        yield [DeclareStrictTypesFixer::class, __DIR__ . '/Source/someDirectory/anotherFile.php', true];
        yield [DeclareStrictTypesFixer::class, __DIR__ . '/Source/someDirectory/anotherFile.php', true];

        yield [FinalInterfaceFixer::class, __DIR__ . '/Source/someFile', false];
        yield [FinalInterfaceFixer::class, __DIR__ . '/Source/someOtherFile', false];
    }

    /**
     * @dataProvider provideCodeAndFile()
     */
    public function testCodeAndFile(string $checker, string $filePath, bool $expected): void
    {
        $this->assertSame($expected, $this->skipper->shouldSkipCodeAndFile($checker, new SmartFileInfo($filePath)));
    }

    public function provideCodeAndFile(): Iterator
    {
        yield [DeclareStrictTypesFixer::class . '.someCode', __DIR__ . '/Source/someFile', true];
        yield [DeclareStrictTypesFixer::class . '.someOtherCode', __DIR__ . '/Source/someDirectory/someFile', true];
        yield [DeclareStrictTypesFixer::class . '.someAnotherCode', __DIR__ . '/Source/someDirectory/someFile', true];

        yield ['someSniff.someForeignCode', __DIR__ . '/Source/someFile', false];
        yield ['someSniff.someOtherCode', __DIR__ . '/Source/someFile', false];
    }

    /**
     * @dataProvider provideMessageAndFile()
     */
    public function testMessageAndFile(string $message, string $filePath, bool $expected): void
    {
        $smartFileInfo = new SmartFileInfo($filePath);

        $this->assertSame($expected, $this->skipper->shouldSkipMessageAndFile($message, $smartFileInfo));
    }

    public function provideMessageAndFile(): Iterator
    {
        yield ['some fishy code at line 5!', __DIR__ . '/Source/someFile', true];
        yield ['some another fishy code at line 5!', __DIR__ . '/Source/someDirectory/someFile.php', true];

        yield [
            'Cognitive complexity for method "foo" is 2 but has to be less than or equal to 1.',
            __DIR__ . '/Source/skip.php.inc',
            true,
        ];
        yield [
            'Cognitive complexity for method "bar" is 2 but has to be less than or equal to 1.',
            __DIR__ . '/Source/skip.php.inc',
            false,
        ];
    }
}
