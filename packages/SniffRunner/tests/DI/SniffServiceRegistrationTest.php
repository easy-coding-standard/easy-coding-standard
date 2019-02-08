<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\DI;

use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class SniffServiceRegistrationTest extends AbstractKernelTestCase
{
    public function test(): void
    {
        static::bootKernelWithConfigs(
            EasyCodingStandardKernel::class,
            [__DIR__ . '/SniffServiceRegistrationSource/easy-coding-standard.yml']
        );

        $sniffFileProcessor = self::$container->get(SniffFileProcessor::class);

        /** @var LineLengthSniff $lineLengthSniff */
        $lineLengthSniff = $sniffFileProcessor->getCheckers()[0];

        $this->assertSame(15, $lineLengthSniff->lineLimit);
        $this->assertSame(['@author'], $lineLengthSniff->absoluteLineLimit);
    }
}
