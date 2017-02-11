<?php declare(strict_types=1);

namespace Symplify\SniffRunner\Tests\File\Provider;

use PHPUnit\Framework\TestCase;
use Symplify\SniffRunner\DI\ContainerFactory;
use Symplify\SniffRunner\File\File;
use Symplify\SniffRunner\File\Provider\FilesProvider;

final class FilesProviderTest extends TestCase
{
    /**
     * @var FilesProvider
     */
    private $filesProvider;

    protected function setUp()
    {
        $container = (new ContainerFactory())->create();
        $this->filesProvider = $container->getByType(FilesProvider::class);
    }

    public function test()
    {
        $source = [__DIR__.'/FilesProviderSource'];
        $files = $this->filesProvider->getFilesForSource($source, false);
        $this->assertCount(1, $files);

        $file = array_pop($files);
        $this->assertInstanceOf(File::class, $file);
    }
}
