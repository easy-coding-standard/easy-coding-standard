<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Finder\SourceFinderSource;

use IteratorAggregate;
use Nette\Utils\Finder;
use Symplify\EasyCodingStandard\Contract\Finder\CustomSourceProviderInterface;

final class PhpAndIncFilesSourceProvider implements CustomSourceProviderInterface
{
    /**
     * @param string[] $source
     */
    public function find(array $source): IteratorAggregate
    {
        return Finder::find('*.php.inc')->in($source);
    }
}
