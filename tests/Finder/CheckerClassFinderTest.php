<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Finder;

use Symplify\EasyCodingStandard\Finder\CheckerClassFinder;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\PackageBuilder\Composer\VendorDirProvider;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class CheckerClassFinderTest extends AbstractKernelTestCase
{
    /**
     * @var CheckerClassFinder
     */
    private $checkerClassFinder;

    protected function setUp(): void
    {
        self::bootKernel(EasyCodingStandardKernel::class);
        $this->checkerClassFinder = self::$container->get(CheckerClassFinder::class);
    }

    public function test(): void
    {
        $vendorDirProvider = new VendorDirProvider();

        $vendorDir = $vendorDirProvider->provide();

        $directories = [];
        $directories[] = $vendorDir . '/squizlabs';
        $directories[] = $vendorDir . '/friendsofphp';

        $checkerClasses = $this->checkerClassFinder->findInDirectories($directories);

        $this->assertGreaterThan(250, $checkerClasses);
    }
}
