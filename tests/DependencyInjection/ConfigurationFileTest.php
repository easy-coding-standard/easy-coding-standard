<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\DependencyInjection;

use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class ConfigurationFileTest extends AbstractKernelTestCase
{
    public function testEmptyConfig(): void
    {
        $this->bootKernelWithConfigs(
            EasyCodingStandardKernel::class,
            [__DIR__ . '/ConfigurationFileSource/empty-config.yml']
        );

        $fixerFileProcessor = self::$container->get(FixerFileProcessor::class);
        $this->assertCount(0, $fixerFileProcessor->getCheckers());

        $sniffFileProcessor = self::$container->get(SniffFileProcessor::class);
        $this->assertCount(0, $sniffFileProcessor->getCheckers());
    }

    public function testIncludeConfig(): void
    {
        $this->bootKernelWithConfigs(
            EasyCodingStandardKernel::class,
            [__DIR__ . '/ConfigurationFileSource/include-another-config.yml']
        );

        $fixerFileProcessor = self::$container->get(FixerFileProcessor::class);
        $this->assertCount(1, $fixerFileProcessor->getCheckers());

        $sniffFileProcessor = self::$container->get(SniffFileProcessor::class);
        $this->assertCount(1, $sniffFileProcessor->getCheckers());
    }
}
