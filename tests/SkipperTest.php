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

    public function testNotSkipped(): void
    {
        $this->assertFalse($this->skipper->shouldSkipCheckerAndFile(
            FinalInterfaceSniff::class,
            __DIR__ . '/someFile'
        ));

        $this->assertFalse($this->skipper->shouldSkipCheckerAndFile(
            FinalInterfaceSniff::class,
            __DIR__ . '/someOtherFile'
        ));

        $this->assertFalse($this->skipper->shouldSkipCodeAndFile('someForeignCode', __DIR__ . 'someFile'));
        $this->assertFalse($this->skipper->shouldSkipCodeAndFile('someOtherCode', __DIR__ . 'someFile'));
    }

    public function testSkipped(): void
    {
        $this->assertTrue($this->skipper->shouldSkipCheckerAndFile(
            DeclareStrictTypesFixer::class,
            __DIR__ . '/someFile'
        ));

        $this->assertTrue($this->skipper->shouldSkipCheckerAndFile(
            DeclareStrictTypesFixer::class,
            __DIR__ . '/someDirectory/anotherFile.php'
        ));

        $this->assertTrue($this->skipper->shouldSkipCheckerAndFile(
            DeclareStrictTypesFixer::class,
            __DIR__ . '/someDirectory/anotherFile.php'
        ));

        $this->assertTrue($this->skipper->shouldSkipCodeAndFile('someCode', __DIR__ . '/someFile'));
        $this->assertTrue($this->skipper->shouldSkipCodeAndFile('someOtherCode', __DIR__ . '/someDirectory/someFile'));
    }

    public function testRemoveFileFromUnused(): void
    {
        $this->skipper->removeFileFromUnused('someFile');

        $this->assertSame([
            DeclareStrictTypesFixer::class => [
                1 => '*/someDirectory/*',
            ],
        ], $this->skipper->getUnusedSkipped());
    }

    private function createParameterProvider(): ParameterProvider
    {
        $container = new Container;
        $container->setParameter('skip', [
            DeclareStrictTypesFixer::class => [
                'someFile',
                '*/someDirectory/*',
            ],
        ]);

        $container->setParameter('skip_codes', [
            'someCode',
            'someOtherCode' => [
                '*/someDirectory/*',
            ],
        ]);

        return new ParameterProvider($container);
    }
}
