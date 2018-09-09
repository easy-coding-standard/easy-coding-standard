<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\DependencyInjection;

use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\EasyCodingStandard\Exception\DependencyInjection\Extension\InvalidSniffPropertyException;
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

        /** @var SniffFileProcessor $sniffFileProcessor */
        $sniffFileProcessor = $container->get(SniffFileProcessor::class);
        $this->assertCount(1, $sniffFileProcessor->getCheckers());
    }

    public function testCreateFromConfigWithMissingProperty(): void
    {
        $this->expectException(InvalidSniffPropertyException::class);
        $this->expectExceptionMessage(sprintf(
            'Property "line_limit" was not found on "%s" sniff class in configuration. Did you mean "lineLimit"?',
            LineLengthSniff::class
        ));

        $this->containerFactory->createWithConfigs(
            [__DIR__ . '/ContainerFactorySource/config-with-typo-in-configuration.yml']
        );
    }
}
