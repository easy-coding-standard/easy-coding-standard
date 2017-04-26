<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\DI;

use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\ClassDeclarationSniff;
use PHPUnit\Framework\TestCase;
use SlevomatCodingStandard\Sniffs\Commenting\ForbiddenAnnotationsSniff;
use Symplify\EasyCodingStandard\Tests\ContainerFactoryWithCustomConfig;

final class SniffServiceRegistrationTest extends TestCase
{
    /**
     * @var LineLengthSniff
     */
    private $lineLengthSniff;

    /**
     * @var ForbiddenAnnotationsSniff
     */
    private $forbiddenAnnotationSniff;

    protected function setUp(): void
    {
        $container = (new ContainerFactoryWithCustomConfig)->createWithConfig(
            __DIR__ . '/SniffServiceRegistrationSource/easy-coding-standard.neon'
        );

        $this->lineLengthSniff = $container->getByType(LineLengthSniff::class);
        $this->forbiddenAnnotationSniff = $container->getByType(ForbiddenAnnotationsSniff::class);
    }

    public function test(): void
    {
        $this->assertSame(15, $this->lineLengthSniff->lineLimit);

        $this->assertSame(
            ['@author'],
            $this->forbiddenAnnotationSniff->forbiddenAnnotations
        );
    }
}