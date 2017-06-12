<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests;

use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symplify\CodingStandard\Sniffs\Classes\FinalInterfaceSniff;
use Symplify\EasyCodingStandard\Configuration\Parameter\ParameterProvider;
use Symplify\EasyCodingStandard\Skipper;
use Symplify\EasyCodingStandard\Validator\CheckerTypeValidator;

final class SkipperTest extends TestCase
{
    /**
     * @var Skipper
     */
    private $skipper;

    protected function setUp(): void
    {
        $this->skipper = new Skipper(
            $this->createParameterProvider(),
            new CheckerTypeValidator
        );
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

    private function createParameterProvider(): ParameterProvider
    {
        $container = new Container();
        $container->setParameter('parameters', [
            'skip' => [
                DeclareStrictTypesFixer::class => ['someFile']
            ]
        ]);

        return new ParameterProvider($container);
    }
}
