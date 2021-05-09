<?php

declare(strict_types=1);

namespace Symplify\Skipper\Tests\Skipper\Only;

use Iterator;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\Skipper\HttpKernel\SkipperKernel;
use Symplify\Skipper\Skipper\Skipper;
use Symplify\Skipper\Tests\Skipper\Only\Source\IncludeThisClass;
use Symplify\Skipper\Tests\Skipper\Only\Source\SkipCompletely;
use Symplify\Skipper\Tests\Skipper\Only\Source\SkipCompletelyToo;
use Symplify\Skipper\Tests\Skipper\Only\Source\SkipThisClass;
use Symplify\SmartFileSystem\SmartFileInfo;

final class OnlySkipperTest extends AbstractKernelTestCase
{
    /**
     * @var Skipper
     */
    private $skipper;

    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(SkipperKernel::class, [__DIR__ . '/config/config.php']);

        $this->skipper = $this->getService(Skipper::class);
    }

    /**
     * @dataProvider provideCheckerAndFile()
     */
    public function testCheckerAndFile(string $class, string $filePath, bool $expected): void
    {
        $resolvedSkip = $this->skipper->shouldSkipElementAndFileInfo($class, new SmartFileInfo($filePath));
        $this->assertSame($expected, $resolvedSkip);
    }

    public function provideCheckerAndFile(): Iterator
    {
        yield [IncludeThisClass::class, __DIR__ . '/Fixture/SomeFileToOnlyInclude.php', false];
        yield [IncludeThisClass::class, __DIR__ . '/Fixture/SomeFile.php', true];

        // no restrictions
        yield [SkipThisClass::class, __DIR__ . '/Fixture/SomeFileToOnlyInclude.php', false];

        yield [SkipThisClass::class, __DIR__ . '/Fixture/SomeFile.php', false];

        yield [SkipCompletely::class, __DIR__ . '/Fixture/SomeFile.php', true];
        yield [SkipCompletelyToo::class, __DIR__ . '/Fixture/SomeFile.php', true];
    }
}
