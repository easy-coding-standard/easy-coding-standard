<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\Tests\DependencyInjection;

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\Strict\StrictParamFixer;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\EasyCodingStandard\Exception\DependencyInjection\Extension\FixerIsNotConfigurableException;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use function Safe\sprintf;

final class FixerServiceRegistrationTest extends TestCase
{
    public function test(): void
    {
        $container = (new ContainerFactory())->createWithConfigs(
            [__DIR__ . '/FixerServiceRegistrationSource/easy-coding-standard.yml']
        );

        /** @var FixerFileProcessor $fixerFileProcessor */
        $fixerFileProcessor = $container->get(FixerFileProcessor::class);

        $checkers = $fixerFileProcessor->getCheckers();
        $this->assertCount(2, $checkers);

        /** @var ArraySyntaxFixer $arraySyntaxFixer */
        $arraySyntaxFixer = $checkers[0];
        $this->assertSame(['syntax' => 'short'], Assert::getObjectAttribute($arraySyntaxFixer, 'configuration'));

        /** @var VisibilityRequiredFixer $visibilityRequiredFixer */
        $visibilityRequiredFixer = $checkers[1];
        $this->assertSame(
            ['elements' => ['property']],
            Assert::getObjectAttribute($visibilityRequiredFixer, 'configuration')
        );
    }

    public function testConfigureUnconfigurableFixer(): void
    {
        $this->expectException(FixerIsNotConfigurableException::class);
        $this->expectExceptionMessage(
            sprintf('Fixer "%s" is not configurable with configuration: {"be_strict":"yea"}.', StrictParamFixer::class)
        );

        (new ContainerFactory())->createWithConfigs(
            [__DIR__ . '/FixerServiceRegistrationSource/non-configurable-fixer.yml']
        );
    }
}
