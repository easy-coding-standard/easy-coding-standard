<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests;

use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PHPUnit\Framework\TestCase;
use Symplify\CodingStandard\Sniffs\Classes\FinalInterfaceSniff;
use Symplify\EasyCodingStandard\Configuration\Contract\Parameter\ParameterProviderInterface;
use Symplify\EasyCodingStandard\Skipper;

final class SkipperTest extends TestCase
{
    /**
     * @var Skipper
     */
    private $skipper;

    protected function setUp(): void
    {
        $this->skipper = new Skipper($this->createParameterProvider());
    }

    public function test(): void
    {
        $this->assertFalse($this->skipper->shouldSkipCheckerAndFile(
            FinalInterfaceSniff::class, 'someFile'
        ));

        $this->assertFalse($this->skipper->shouldSkipCheckerAndFile(
            FinalInterfaceSniff::class, 'someOtherFile'
        ));

        $this->assertTrue($this->skipper->shouldSkipCheckerAndFile(
            DeclareStrictTypesFixer::class, 'someFile'
        ));
    }

    private function createParameterProvider(): ParameterProviderInterface
    {
        return new class implements ParameterProviderInterface
        {
            /**
             * @return mixed[]
             */
            public function provide(): array
            {
                return [
                    'skip' => [
                        DeclareStrictTypesFixer::class => ['someFile']
                    ]
                ];
            }
        };
    }
}
