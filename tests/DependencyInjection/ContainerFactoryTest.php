<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;

final class ContainerFactoryTest extends TestCase
{
    /**
     * @var ContainerFactory
     */
    private $containerFactory;

    protected function setUp(): void
    {
        $this->containerFactory = new ContainerFactory();
    }

    public function testCreate(): void
    {
        $container = $this->containerFactory->create();
        $this->assertInstanceOf(ContainerInterface::class, $container);
    }

    public function testCreateFromConfig(): void
    {
        $container = $this->containerFactory->createWithConfigs(
            [__DIR__ . '/ContainerFactorySource/normal-config.yml']
        );
        $this->assertInstanceOf(ContainerInterface::class, $container);

        $sniffFileProcessor = $container->get(SniffFileProcessor::class);
        $this->assertCount(1, $sniffFileProcessor->getCheckers());
    }
}
