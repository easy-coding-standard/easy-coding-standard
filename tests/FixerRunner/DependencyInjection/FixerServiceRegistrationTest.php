<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\FixerRunner\DependencyInjection;

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\Testing\PHPUnit\AbstractTestCase;
use Symplify\EasyCodingStandard\Utils\PrivatesAccessorHelper;

final class FixerServiceRegistrationTest extends AbstractTestCase
{
    public function test(): void
    {
        $this->createContainerWithConfigs([__DIR__ . '/config/easy-coding-standard.php']);
        $fixerFileProcessor = $this->make(FixerFileProcessor::class);

        $checkers = $fixerFileProcessor->getCheckers();
        $this->assertCount(2, $checkers);

        /** @var ArraySyntaxFixer $arraySyntaxFixer */
        $arraySyntaxFixer = $checkers[1];
        $this->assertInstanceOf(ArraySyntaxFixer::class, $arraySyntaxFixer);

        $arraySyntaxConfiguration = PrivatesAccessorHelper::getPropertyValue($arraySyntaxFixer, 'configuration');
        $this->assertSame([
            'syntax' => 'short',
        ], $arraySyntaxConfiguration);

        /** @var VisibilityRequiredFixer $visibilityRequiredFixer */
        $visibilityRequiredFixer = $checkers[0];
        $this->assertInstanceOf(VisibilityRequiredFixer::class, $visibilityRequiredFixer);

        $visibilityRequiredConfiguration = PrivatesAccessorHelper::getPropertyValue(
            $visibilityRequiredFixer,
            'configuration'
        );

        $this->assertSame([
            'elements' => ['property'],
        ], $visibilityRequiredConfiguration);
    }
}
