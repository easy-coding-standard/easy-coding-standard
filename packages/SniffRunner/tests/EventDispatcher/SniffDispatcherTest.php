<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\EventDispatcher;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\ClassDeclarationSniff;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\SniffRunner\TokenDispatcher\Event\FileTokenEvent;
use Symplify\EasyCodingStandard\SniffRunner\TokenDispatcher\TokenDispatcher;
use Symplify\PackageBuilder\Adapter\Nette\GeneralContainerFactory;

final class tokenDispatcherTest extends TestCase
{
    /**
     * @var TokenDispatcher
     */
    private $tokenDispatcher;

    protected function setUp(): void
    {
        $container = (new GeneralContainerFactory)->createFromConfig(__DIR__ . '/../../../../src/config/config.neon');
        $this->tokenDispatcher = $container->getByType(TokenDispatcher::class);
    }

    public function testDispatch(): void
    {
        $sniffs = [new ClassDeclarationSniff];
        $this->tokenDispatcher->addSniffListeners($sniffs);

        $fileMock = $this->prophesize(File::class)
            ->reveal();

        $event = new FileTokenEvent($fileMock, 5);
        $this->tokenDispatcher->dispatchToken(T_CLASS, $event);
        $this->assertSame(5, $event->getPosition());
    }
}
