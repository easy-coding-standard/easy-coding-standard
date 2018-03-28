<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\Sniff\Finder;

use Symplify\EasyCodingStandard\SniffRunner\Sniff\Finder\SniffFinder;
use Symplify\EasyCodingStandard\Tests\AbstractContainerAwareTestCase;
use Symplify\PackageBuilder\Composer\VendorDirProvider;

final class SniffFinderTest extends AbstractContainerAwareTestCase
{
    public function test(): void
    {
        /** @var SniffFinder $sniffFinder */
        $sniffFinder = $this->container->get(SniffFinder::class);
        $this->assertGreaterThan(250, $sniffFinder->findAllSniffClassesInDirectory(VendorDirProvider::provide()));
    }
}
