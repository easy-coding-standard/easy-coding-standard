<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration\Tests\Parameter;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Configuration\Parameter\ParameterProvider;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;

final class ParameterProviderTest extends TestCase
{
    public function test(): void
    {
        $container = (new ContainerFactory)->createWithConfig(
            __DIR__ . '/ParameterProviderSource/easy-coding-standard.neon'
        );

        $parameterProvider = $container->get(ParameterProvider::class);
        $this->assertSame(['key' => 'value'], $parameterProvider->provide());
    }
}
