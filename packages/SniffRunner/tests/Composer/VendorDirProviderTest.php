<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\Composer;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\SniffRunner\Composer\VendorDirProvider;

final class VendorDirProviderTest extends TestCase
{
    public function testProvide(): void
    {
        $this->assertStringEndsWith('vendor', VendorDirProvider::provide());
        $this->assertStringEndsWith('vendor', VendorDirProvider::provide());

        $this->assertFileExists(VendorDirProvider::provide() . '/autoload.php');
    }
}
