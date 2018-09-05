<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;

final class ExcludedCheckersTest extends TestCase
{
    public function test(): void
    {
        $container = (new ContainerFactory())->createWithConfig(__DIR__ . '/ExcludedCheckersSource/config.yml');

        /** @var FixerFileProcessor $fixerFileProcessor */
        $fixerFileProcessor = $container->get(FixerFileProcessor::class);
        $this->assertCount(0, $fixerFileProcessor->getCheckers());
    }
}
