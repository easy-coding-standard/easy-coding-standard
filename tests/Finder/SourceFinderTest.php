<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Finder;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Finder\SourceFinder;

final class SourceFinderTest extends TestCase
{
    public function test()
    {
        $sourceFinder = new SourceFinder();
        $foundFiles = $sourceFinder->find([__DIR__ . '/SourceFinderSource']);
        $this->assertCount(1, $foundFiles);

        $foundFiles = $sourceFinder->find([__DIR__ . '/SourceFinderSource/SomeClass.php.inc']);
        $this->assertCount(1, $foundFiles);
    }
}
