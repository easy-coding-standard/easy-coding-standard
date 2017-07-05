<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests;

use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symplify\CodingStandard\Sniffs\Classes\FinalInterfaceSniff;
use Symplify\EasyCodingStandard\Skipper;
use Symplify\EasyCodingStandard\Validator\CheckerTypeValidator;
use Symplify\PackageBuilder\Adapter\Symfony\Parameter\ParameterProvider;

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

    public function testRemoveFileFromUnused(): void
    {
        $this->assertSame([DeclareStrictTypesFixer::class => ['someFile']], $this->skipper->getUnusedSkipped());
        $this->skipper->removeFileFromUnused('someFile');
        $this->assertSame([], $this->skipper->getUnusedSkipped());
    }

    private function createParameterProvider(): ParameterProvider
    {
        $container = new Container;
        $container->setParameter('skip', [
            DeclareStrictTypesFixer::class => ['someFile'],
        ]);

        return new ParameterProvider($container);
    }
}
