<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Configuration;

use Nette\DI\Config\Loader;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Configuration\ConfigurationFileLoader;

final class ConfigurationFileLoaderTest extends TestCase
{
    public function test(): void
    {
        $multiCsFileLoader = new ConfigurationFileLoader(
            __DIR__ . '/coding-standard.neon',
            new Loader
        );

        $loadedFile = $multiCsFileLoader->load();
        $this->assertSame([
           'key' => 'value',
        ], $loadedFile);
    }
}
