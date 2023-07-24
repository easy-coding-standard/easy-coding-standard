<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Skipper\Skipper\Skipper;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\EasyCodingStandard\Skipper\Skipper\Skipper;
use Symplify\EasyCodingStandard\Tests\Skipper\Skipper\Skipper\Fixture\Element\FifthElement;
use Symplify\EasyCodingStandard\Tests\Skipper\Skipper\Skipper\Fixture\Element\SixthSense;
use Symplify\EasyCodingStandard\Tests\Skipper\Skipper\Skipper\Fixture\Element\ThreeMan;
use Symplify\EasyCodingStandard\Tests\Testing\AbstractTestCase;

final class SkipperTest extends AbstractTestCase
{
    private Skipper $skipper;

    protected function setUp(): void
    {
        $this->createContainerWithConfigs([__DIR__ . '/config/config.php']);
        $this->skipper = $this->make(Skipper::class);
    }

    #[DataProvider('provideDataShouldSkipFileInfo')]
    public function testSkipFileInfo(string $filePath, bool $expectedSkip): void
    {
        $resultSkip = $this->skipper->shouldSkipFilePath($filePath);
        $this->assertSame($expectedSkip, $resultSkip);
    }

    /**
     * @return Iterator<string[]|bool[]>
     */
    public static function provideDataShouldSkipFileInfo(): Iterator
    {
        yield [__DIR__ . '/Fixture/SomeRandom/file.txt', false];
        yield [__DIR__ . '/Fixture/SomeSkipped/any.txt', true];

        $basenameCwd = basename(getcwd());
        if ($basenameCwd === 'easy-coding-standard') {
            // split test inside packages/easy-coding-standard
            yield ['packages-tests/Skipper/Skipper/Skipper/Fixture/SomeSkipped/any.txt', true];
        } else {
            // from root symplify
            yield [
                'packages/easy-coding-standard/packages-tests/Skipper/Skipper/Skipper/Fixture/SomeSkipped/any.txt',
                true,
            ];
        }
    }

    /**
     * @param object|class-string $element
     */
    #[DataProvider('provideDataShouldSkipElement')]
    public function testSkipElement(string|object $element, bool $expectedSkip): void
    {
        $resultSkip = $this->skipper->shouldSkipElement($element);
        $this->assertSame($expectedSkip, $resultSkip);
    }

    /**
     * @return Iterator<bool[]|class-string<SixthSense>[]|class-string<ThreeMan>[]|FifthElement[]>
     */
    public static function provideDataShouldSkipElement(): Iterator
    {
        yield [ThreeMan::class, false];
        yield [SixthSense::class, true];
        yield [new FifthElement(), true];
    }
}
