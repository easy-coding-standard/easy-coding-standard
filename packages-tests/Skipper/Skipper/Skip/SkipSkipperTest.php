<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Skipper\Skipper\Skip;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\EasyCodingStandard\Kernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\Skipper\Skipper\Skipper;
use Symplify\EasyCodingStandard\Tests\Skipper\Skipper\Skip\Source\AnotherClassToSkip;
use Symplify\EasyCodingStandard\Tests\Skipper\Skipper\Skip\Source\NotSkippedClass;
use Symplify\EasyCodingStandard\Tests\Skipper\Skipper\Skip\Source\SomeClassToSkip;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class SkipSkipperTest extends AbstractKernelTestCase
{
    private Skipper $skipper;

    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(EasyCodingStandardKernel::class, [__DIR__ . '/config/config.php']);
        $this->skipper = $this->getService(Skipper::class);
    }

    #[DataProvider('provideCheckerAndFile')]
    #[DataProvider('provideCodeAndFile')]
    #[DataProvider('provideMessageAndFile')]
    #[DataProvider('provideAnythingAndFilePath')]
    public function test(string $element, string $filePath, bool $expectedSkip): void
    {
        $resolvedSkip = $this->skipper->shouldSkipElementAndFilePath($element, $filePath);
        $this->assertSame($expectedSkip, $resolvedSkip);
    }

    /**
     * @return Iterator<string[]|bool[]|class-string<AnotherClassToSkip>[]|class-string<NotSkippedClass>[]|class-string<SomeClassToSkip>[]>
     */
    public static function provideCheckerAndFile(): Iterator
    {
        yield [SomeClassToSkip::class, __DIR__ . '/Fixture', true];

        yield [AnotherClassToSkip::class, __DIR__ . '/Fixture/someFile', true];
        yield [AnotherClassToSkip::class, __DIR__ . '/Fixture/someDirectory/anotherFile.php', true];
        yield [AnotherClassToSkip::class, __DIR__ . '/Fixture/someDirectory/anotherFile.php', true];

        yield [NotSkippedClass::class, __DIR__ . '/Fixture/someFile', false];
        yield [NotSkippedClass::class, __DIR__ . '/Fixture/someOtherFile', false];
    }

    /**
     * @return Iterator<string[]|bool[]>
     */
    public static function provideCodeAndFile(): Iterator
    {
        yield [AnotherClassToSkip::class . '.someCode', __DIR__ . '/Fixture/someFile', true];
        yield [AnotherClassToSkip::class . '.someOtherCode', __DIR__ . '/Fixture/someDirectory/someFile', true];
        yield [AnotherClassToSkip::class . '.someAnotherCode', __DIR__ . '/Fixture/someDirectory/someFile', true];

        yield ['someSniff.someForeignCode', __DIR__ . '/Fixture/someFile', false];
        yield ['someSniff.someOtherCode', __DIR__ . '/Fixture/someFile', false];
    }

    /**
     * @return Iterator<string[]|bool[]>
     */
    public static function provideMessageAndFile(): Iterator
    {
        yield ['some fishy code at line 5!', __DIR__ . '/Fixture/someFile', true];
        yield ['some another fishy code at line 5!', __DIR__ . '/Fixture/someDirectory/someFile.php', true];

        yield [
            'Cognitive complexity for method "foo" is 2 but has to be less than or equal to 1.',
            __DIR__ . '/Fixture/skip.php.inc',
            true,
        ];
        yield [
            'Cognitive complexity for method "bar" is 2 but has to be less than or equal to 1.',
            __DIR__ . '/Fixture/skip.php.inc',
            false,
        ];
    }

    /**
     * @return Iterator<string[]|bool[]>
     */
    public static function provideAnythingAndFilePath(): Iterator
    {
        yield ['anything', __DIR__ . '/Fixture/AlwaysSkippedPath/some_file.txt', true];
        yield ['anything', __DIR__ . '/Fixture/PathSkippedWithMask/another_file.txt', true];
    }
}
