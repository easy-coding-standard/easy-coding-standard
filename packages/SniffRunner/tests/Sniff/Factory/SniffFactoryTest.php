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
        $container = (new GeneralContainerFactory())->createFromConfig(__DIR__ . '/../../../../../src/config/config.neon');
        $this->sniffFactory = $container->getByType(SniffFactory::class);
    }

    /**
     * @expectedException \Symplify\EasyCodingStandard\SniffRunner\Exception\ClassNotFoundException
     */
    public function testCreateInvalidClassName()
    {
        $this->sniffFactory->create('mmissing');
    }

    public function testCreate()
    {
        $sniff = $this->sniffFactory->create(ClassDeclarationSniff::class);
        $this->assertInstanceOf(ClassDeclarationSniff::class, $sniff);
    }

    public function testPropertiesAreChanged()
    {
        /** @var LineLengthSniff $lineLenghtSniff */
        $lineLenghtSniff = $this->sniffFactory->create(LineLengthSniff::class);
        $this->assertSame(80, $lineLenghtSniff->lineLimit);
        $this->assertSame(100, $lineLenghtSniff->absoluteLineLimit);

        // @todo
    }
}
