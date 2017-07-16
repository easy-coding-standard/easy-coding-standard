<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Finder\SourceFinderSource;

use Nette\Utils\Finder;
use SplFileInfo;
use Symplify\EasyCodingStandard\Contract\Finder\ExtraFilesProviderInterface;

final class IncFilesExtraFilesProvider implements ExtraFilesProviderInterface
{
    /**
     * @param string[] $source
     * @return SplFileInfo[]
     */
    public function provideForSource(array $source): array
    {
        $finder = Finder::find('*.php.inc')->in($source);

        return iterator_to_array($finder->getIterator());
    }
}
