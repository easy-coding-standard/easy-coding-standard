<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\Tests\DependencyInjection;

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\Strict\StrictParamFixer;
use Symplify\EasyCodingStandard\Exception\DependencyInjection\Extension\FixerIsNotConfigurableException;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class FixerServiceRegistrationTest extends AbstractKernelTestCase
{
    /**
     * @var PrivatesAccessor
     */
    private $privatesAccessor;

    protected function setUp(): void
    {
        $this->privatesAccessor = new PrivatesAccessor();
    }

    public function test(): void
    {
        $this->bootKernelWithConfigs(
            EasyCodingStandardKernel::class,
            [__DIR__ . '/FixerServiceRegistrationSource/easy-coding-standard.yml']
        );

        $fixerFileProcessor = self::$container->get(FixerFileProcessor::class);

        $checkers = $fixerFileProcessor->getCheckers();
        $this->assertCount(2, $checkers);

        /** @var ArraySyntaxFixer $arraySyntaxFixer */
        $arraySyntaxFixer = $checkers[0];
        $this->assertInstanceOf(ArraySyntaxFixer::class, $arraySyntaxFixer);

        $configuration = $this->privatesAccessor->getPrivateProperty($arraySyntaxFixer, 'configuration');
        $this->assertSame(['syntax' => 'short'], $configuration);

        /** @var VisibilityRequiredFixer $visibilityRequiredFixer */
        $visibilityRequiredFixer = $checkers[1];
        $this->assertInstanceOf(VisibilityRequiredFixer::class, $visibilityRequiredFixer);

        $configuration = $this->privatesAccessor->getPrivateProperty($visibilityRequiredFixer, 'configuration');
        $this->assertSame([
            'elements' => ['property'],
        ], $configuration);
    }

    public function testConfigureUnconfigurableFixer(): void
    {
        $this->expectException(FixerIsNotConfigurableException::class);
        $this->expectExceptionMessage(
            sprintf('Fixer "%s" is not configurable with configuration: {"be_strict":"yea"}.', StrictParamFixer::class)
        );

        $this->bootKernelWithConfigs(
            EasyCodingStandardKernel::class,
            [__DIR__ . '/FixerServiceRegistrationSource/non-configurable-fixer.yml']
        );
    }
}
