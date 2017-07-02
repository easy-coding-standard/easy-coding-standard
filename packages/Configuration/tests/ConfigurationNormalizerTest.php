<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration\Tests;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Configuration\CheckerConfigurationNormalizer;
use Symplify\EasyCodingStandard\Configuration\Exception\DuplicatedCheckerFoundException;
use Symplify\EasyCodingStandard\Configuration\Exception\InvalidConfigurationTypeException;

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

    public function testDuplicates(): void
    {
        $this->expectException(DuplicatedCheckerFoundException::class);
        $this->expectExceptionMessage(
            'Checker "sniff" is being registered twice. Keep it only once, '
            . 'so configuration is clear and performance better.'
        );

        $this->configurationNormalizer->normalize([
            0 => 'sniff',
            'sniff' => null,
        ]);
    }

    public function testNonArrayConfiguration(): void
    {
        $this->expectException(InvalidConfigurationTypeException::class);
        $this->expectExceptionMessage(
            'Configuration of "sniff" checker has to be array; ' .
            '"string" given with "configuration".'
        );
        $this->configurationNormalizer->normalize([
            'sniff' => 'configuration',
        ]);
    }
}
