<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Configuration;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Configuration\ConfigurationNormalizer;

final class ConfigurationNormalizerTest extends TestCase
{
    /**
     * @var ConfigurationNormalizer
     */
    private $configurationNormalizer;

    protected function setUp(): void
    {
        $this->configurationNormalizer = new ConfigurationNormalizer;
    }

    public function test(): void
    {
        $normalizedConfiguration = $this->configurationNormalizer->normalizeClassesConfiguration([
            0 => 'sniff',
            'sniffAndItsConfig' => ['key' => 'value']
        ]);

        $this->assertSame([
            'sniff' => [],
            'sniffAndItsConfig' => [
                'key' => 'value'
            ]
        ], $normalizedConfiguration);
    }
}
