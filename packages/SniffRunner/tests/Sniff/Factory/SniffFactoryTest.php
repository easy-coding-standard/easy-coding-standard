<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\Sniff\Factory;

use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\ClassDeclarationSniff;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\SniffRunner\Sniff\Factory\SniffFactory;
use Symplify\PackageBuilder\Adapter\Nette\GeneralContainerFactory;

final class SniffFactoryTest extends TestCase
{
    /**
     * @var SniffFactory
     */
    private $sniffFactory;

    protected function setUp()
    {
        $container = (new GeneralContainerFactory)->createFromConfig(
            __DIR__ . '/../../../../../src/config/config.neon'
        );
        $this->sniffFactory = $container->getByType(SniffFactory::class);
    }

    public function testCreateFromClasses()
    {
        $sniffs = $this->sniffFactory->createFromClasses([ClassDeclarationSniff::class]);
        $this->assertInstanceOf(ClassDeclarationSniff::class, $sniffs[0]);
    }

    public function testPropertiesAreChanged()
    {
        $sniffs = $this->sniffFactory->createFromClasses([LineLengthSniff::class => [
            'lineLimit' => 15
        ]]);

        /** @var LineLengthSniff $lineLengthSniff */
        $lineLengthSniff = $sniffs[0];

        $this->assertSame(15, $lineLengthSniff->lineLimit);
    }
}
