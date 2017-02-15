<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\EventDispatcher;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\ClassDeclarationSniff;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\SniffRunner\EventDispatcher\Event\CheckFileTokenEvent;
use Symplify\EasyCodingStandard\SniffRunner\EventDispatcher\SniffDispatcher;
use Symplify\PackageBuilder\Adapter\Nette\ContainerFactory;

final class SniffDispatcherTest extends TestCase
{
    /**
     * @var SniffDispatcher
     */
    private $sniffDispatcher;

    protected function setUp()
    {
        $container = (new ContainerFactory())->createFromConfig(__DIR__ . '/../../src/config/config.neon');
        $this->sniffDispatcher = $container->getByType(SniffDispatcher::class);
    }

    public function testAddSniffListeners()
    {
        $sniffs = [new ClassDeclarationSniff()];
        $this->sniffDispatcher->addSniffListeners($sniffs);

        $this->assertCount(3, $this->sniffDispatcher->getListeners());
        $this->assertCount(1, $this->sniffDispatcher->getListeners(T_CLASS));
    }

    public function testDispatch()
    {
        $sniffs = [new ClassDeclarationSniff()];
        $this->sniffDispatcher->addSniffListeners($sniffs);

        $fileMock = $this->prophesize(File::class)
            ->reveal();

        $event = new CheckFileTokenEvent($fileMock, 5);
        $this->sniffDispatcher->dispatch(T_CLASS, $event);
        $this->assertSame(5,$event->getStackPointer());
    }
}
