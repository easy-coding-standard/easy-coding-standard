<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Finder\SourceFinderSource;

use Nette\Utils\Finder;
use Symplify\EasyCodingStandard\Contract\Finder\CustomSourceProviderInterface;

final class PhpAndIncFilesSourceProvider implements CustomSourceProviderInterface
{
    /**
     * @param string[] $source
     */
    public function find(array $source): Finder
    {
        return Finder::find('*.php.inc')->in($source);
    }
}
