<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Finder;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Finder\CheckerClassFinder;
use Symplify\PackageBuilder\Composer\StaticVendorDirProvider;

final class CheckerClassFinderTest extends TestCase
{
    public function test(): void
    {
        $checkerClassFinder = new CheckerClassFinder();

        $vendorDir = StaticVendorDirProvider::provide();
        $directories = [];
        $directories[] = $vendorDir . '/squizlabs';
        $directories[] = $vendorDir . '/friendsofphp';

        $checkerClasses = $checkerClassFinder->findInDirectories($directories);

        $this->assertGreaterThan(250, $checkerClasses);
    }
}
