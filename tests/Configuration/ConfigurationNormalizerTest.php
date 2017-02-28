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

    protected function setUp()
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

    public function testSkipperRulesInverts(): void
    {
        $fileFirst = [
            'packages/EasyCodingStandard/packages/SniffRunner/src/File/File.php' =>
                ['SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff']
        ];
        $normalizedFileFirst = $this->configurationNormalizer->normalizeSkipperConfiguration($fileFirst);

        $classFirst = [
            'SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff' =>
                ['packages/EasyCodingStandard/packages/SniffRunner/src/File/File.php']
        ];
        $normalizedClassFirst = $this->configurationNormalizer->normalizeSkipperConfiguration($classFirst);

        $this->assertSame($normalizedFileFirst, $normalizedClassFirst);
    }
}
