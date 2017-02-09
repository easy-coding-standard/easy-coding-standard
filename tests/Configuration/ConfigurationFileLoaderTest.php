<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Configuration;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Configuration\ConfigurationFileLoader;

final class ConfigurationFileLoaderTest extends TestCase
{
    public function test()
    {
        $multiCsFileLoader = new ConfigurationFileLoader(__DIR__.'/coding-standard-key-value.json');

        $loadedFile = $multiCsFileLoader->load();
        $this->assertSame([
           'key' => 'value',
        ], $loadedFile);
    }
}
