<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\Sniff\Finder;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\SniffRunner\DI\ContainerFactory;
use Symplify\EasyCodingStandard\SniffRunner\Sniff\Finder\SniffFinder;

final class SniffFinderTest extends TestCase
{
    public function test()
    {
        $container = (new ContainerFactory())->create();
        $sniffFinder = $container->getByType(SniffFinder::class);
        $this->assertGreaterThan(250, $sniffFinder->findAllSniffClasses());
    }
}
