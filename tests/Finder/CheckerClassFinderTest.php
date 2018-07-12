<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Finder;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Finder\CheckerClassFinder;
use Symplify\PackageBuilder\Composer\VendorDirProvider;

final class CheckerClassFinderTest extends TestCase
{
    /**
     * @var CheckerClassFinder
     */
    private $checkerClassFinder;

    protected function setUp(): void
    {
        $this->checkerClassFinder = new CheckerClassFinder();
    }

    public function test(): void
    {
        $checkerClasses = $this->checkerClassFinder->findInDirectories([VendorDirProvider::provide()]);

        $this->assertGreaterThan(250, $checkerClasses);
    }
}
