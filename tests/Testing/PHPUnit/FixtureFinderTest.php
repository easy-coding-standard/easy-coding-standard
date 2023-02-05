<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Testing\PHPUnit;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Testing\PHPUnit\FixtureFinder;

final class FixtureFinderTest extends TestCase
{
    public function test(): void
    {
        $iterator = FixtureFinder::yieldDataProviderFiles(__DIR__ . '/Fixture');
        $this->assertCount(2, iterator_to_array($iterator));
    }
}
