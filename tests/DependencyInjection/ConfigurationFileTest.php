<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;

final class ConfigurationFileTest extends TestCase
{
    public function testEmptyConfig(): void
    {
        $container = (new ContainerFactory())->createWithConfig(__DIR__ . '/ConfigurationFileSource/empty-config.yml');

        /** @var FixerFileProcessor $fixerFileProcessor */
        $fixerFileProcessor = $container->get(FixerFileProcessor::class);
        $this->assertCount(0, $fixerFileProcessor->getCheckers());

        /** @var SniffFileProcessor $sniffFileProcessor */
        $sniffFileProcessor = $container->get(SniffFileProcessor::class);
        $this->assertCount(0, $sniffFileProcessor->getCheckers());
    }

    public function testIncludeConfig(): void
    {
        $container = (new ContainerFactory())->createWithConfig(
            __DIR__ . '/ConfigurationFileSource/include-another-config.yml'
        );

        /** @var FixerFileProcessor $fixerFileProcessor */
        $fixerFileProcessor = $container->get(FixerFileProcessor::class);
        $this->assertCount(1, $fixerFileProcessor->getCheckers());

        /** @var SniffFileProcessor $sniffFileProcessor */
        $sniffFileProcessor = $container->get(SniffFileProcessor::class);
        $this->assertCount(1, $sniffFileProcessor->getCheckers());
    }
}
