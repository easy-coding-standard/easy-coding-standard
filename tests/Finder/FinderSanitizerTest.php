<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Finder;

use Nette\Utils\Finder as NetteFinder;
use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symfony\Component\Finder\Finder as SymfonyFinder;
use Symplify\EasyCodingStandard\Exception\Finder\InvalidSourceTypeException;
use Symplify\EasyCodingStandard\Finder\FinderSanitizer;

final class FinderSanitizerTest extends TestCase
{
    /**
     * @var FinderSanitizer
     */
    private $finderSanitizer;

    protected function setUp(): void
    {
        $this->finderSanitizer = new FinderSanitizer();
    }

    public function testValidTypes(): void
    {
        $this->expectException(InvalidSourceTypeException::class);
        $files = [new SplFileInfo(__DIR__ . '/FinderSanitizerSource/FileWithClass.php')];
        $this->finderSanitizer->sanitize($files);
    }

    public function testSymfonyFinder(): void
    {
        $finder = SymfonyFinder::create()
            ->files()
            ->in(__DIR__ . '/FinderSanitizerSource');

        $this->assertCount(2, iterator_to_array($finder->getIterator()));

        $files = $this->finderSanitizer->sanitize($finder);
        $this->assertCount(1, $files);
    }

    public function testNetteFinder(): void
    {
        $finder = NetteFinder::find('*')->in(__DIR__ . '/FinderSanitizerSource');

        $this->assertCount(2, iterator_to_array($finder->getIterator()));

        $files = $this->finderSanitizer->sanitize($finder);
        $this->assertCount(1, $files);
    }
}
