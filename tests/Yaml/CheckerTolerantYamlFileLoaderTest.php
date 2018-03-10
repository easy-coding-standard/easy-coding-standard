<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Yaml;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;

final class CheckerTolerantYamlFileLoaderTest extends TestCase
{
    public function test(): void
    {
        $container = (new ContainerFactory())->createWithConfig(__DIR__ . '/CheckerTolerantYamlFileLoaderSource/config.yml');

        /** @var FixerFileProcessor $fixerFileProcessor */
        $fixerFileProcessor = $container->get(FixerFileProcessor::class);
        $this->assertCount(1, $fixerFileProcessor->getCheckers());
    }
}
