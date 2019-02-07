<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Finder;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Finder\CheckerClassFinder;
use Symplify\PackageBuilder\Composer\VendorDirProvider;

final class CheckerClassFinderTest extends TestCase
{
    public function test(): void
    {
        $checkerClassFinder = new CheckerClassFinder();
        $checkerClasses = $checkerClassFinder->findInDirectories([VendorDirProvider::provide()]);

        $this->assertGreaterThan(250, $checkerClasses);
    }
}
