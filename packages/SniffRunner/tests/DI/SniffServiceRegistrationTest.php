<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\DI;

use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;

final class SniffServiceRegistrationTest extends TestCase
{
    /**
     * @var LineLengthSniff
     */
    private $lineLengthSniff;

    protected function setUp(): void
    {
        $container = (new ContainerFactory())->createWithConfig(
            __DIR__ . '/SniffServiceRegistrationSource/easy-coding-standard.neon'
        );

        $this->lineLengthSniff = $container->get(LineLengthSniff::class);
    }

    public function test(): void
    {
        $this->assertSame(15, $this->lineLengthSniff->lineLimit);

        $this->assertSame(
            ['@author'],
            $this->lineLengthSniff->absoluteLineLimit
        );
    }
}
