<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\DI;

use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;

final class SniffServiceRegistrationTest extends TestCase
{
    public function test(): void
    {
        $container = (new ContainerFactory())->createWithConfigs(
            [__DIR__ . '/SniffServiceRegistrationSource/easy-coding-standard.yml']
        );

        /** @var SniffFileProcessor $sniffFileProcessor */
        $sniffFileProcessor = $container->get(SniffFileProcessor::class);

        /** @var LineLengthSniff $lineLengthSniff */
        $lineLengthSniff = $sniffFileProcessor->getCheckers()[0];

        $this->assertSame(15, $lineLengthSniff->lineLimit);
        $this->assertSame(['@author'], $lineLengthSniff->absoluteLineLimit);
    }
}
