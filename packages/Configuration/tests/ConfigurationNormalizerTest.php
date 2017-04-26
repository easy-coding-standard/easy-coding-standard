<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Configuration;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Configuration\CheckerConfigurationNormalizer;

final class ConfigurationNormalizerTest extends TestCase
{
    /**
     * @var CheckerConfigurationNormalizer
     */
    private $configurationNormalizer;

    protected function setUp(): void
    {
        $this->configurationNormalizer = new CheckerConfigurationNormalizer;
    }

    public function test(): void
    {
        $normalizedConfiguration = $this->configurationNormalizer->normalize([
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
