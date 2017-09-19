<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration\Tests;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Configuration\CheckerConfigurationNormalizer;
use Symplify\EasyCodingStandard\Configuration\Exception\InvalidConfigurationTypeException;

final class ConfigurationNormalizerTest extends TestCase
{
    /**
     * @var CheckerConfigurationNormalizer
     */
    private $checkerConfigurationNormalizer;

    protected function setUp(): void
    {
        $this->checkerConfigurationNormalizer = new CheckerConfigurationNormalizer;
    }

    public function test(): void
    {
        $normalizedConfiguration = $this->checkerConfigurationNormalizer->normalize([
            0 => 'sniff',
            'someSniffWithCommentedConfig' => null,
            'sniffAndItsConfig' => ['key' => 'value'],
        ]);

        $this->assertSame([
            'sniff' => [],
            'someSniffWithCommentedConfig' => [],
            'sniffAndItsConfig' => [
                'key' => 'value',
            ],
        ], $normalizedConfiguration);
    }

    public function testNonArrayConfiguration(): void
    {
        $this->expectException(InvalidConfigurationTypeException::class);
        $this->expectExceptionMessage(
            'Configuration of "sniff" checker has to be array; ' .
            '"string" given with "configuration".'
        );
        $this->checkerConfigurationNormalizer->normalize([
            'sniff' => 'configuration',
        ]);
    }

    public function testMerging(): void
    {
        $normalizedConfiguration = $this->checkerConfigurationNormalizer->normalize([
            0 => 'sniff',
            'sniff' => [
                'key' => 'value',
            ],
        ]);

        $this->assertSame([
            'sniff' => [
                'key' => 'value',
            ],
        ], $normalizedConfiguration);
    }
}
