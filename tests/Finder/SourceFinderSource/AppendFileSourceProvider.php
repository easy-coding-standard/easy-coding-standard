<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Finder\SourceFinderSource;

use IteratorAggregate;
use Symfony\Component\Finder\Finder;
use Symplify\EasyCodingStandard\Contract\Finder\CustomSourceProviderInterface;

final class AppendFileSourceProvider implements CustomSourceProviderInterface
{
    /**
     * @param string[] $source
     */
    public function find(array $source): IteratorAggregate
    {
        return Finder::create()
            ->name('#\.php\.inc$#')
            ->in(__DIR__ . '/Source')
            ->append([__DIR__ . '/Source/SomeClass.php']);
    }
}
