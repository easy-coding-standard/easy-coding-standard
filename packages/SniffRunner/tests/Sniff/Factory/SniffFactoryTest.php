<?php declare(strict_types=1);

namespace Symplify\SniffRunner\Tests\Sniff\Factory;

use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\ClassDeclarationSniff;
use PHPUnit\Framework\TestCase;
use Symplify\SniffRunner\DI\ContainerFactory;
use Symplify\SniffRunner\Sniff\Factory\SniffFactory;

final class SniffFactoryTest extends TestCase
{
    /**
     * @var SniffFactory
     */
    private $sniffFactory;

    protected function setUp()
    {
        $container = (new ContainerFactory())->create();
        $this->sniffFactory = $container->getByType(SniffFactory::class);
    }

    /**
     * @expectedException \Symplify\SniffRunner\Exception\ClassNotFoundException
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
