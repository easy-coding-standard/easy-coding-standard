<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\FixerRunner\DependencyInjection;

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\Kernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\Tests\Testing\AbstractTestCase;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class FixerServiceRegistrationTest extends AbstractTestCase
{
    public function test(): void
    {
        $privatesAccessor = new PrivatesAccessor();

        $this->createContainerWithConfigs([__DIR__ . '/config/easy-coding-standard.php']);
        $fixerFileProcessor = $this->make(FixerFileProcessor::class);

        $checkers = $fixerFileProcessor->getCheckers();
        $this->assertCount(2, $checkers);

        /** @var ArraySyntaxFixer $arraySyntaxFixer */
        $arraySyntaxFixer = $checkers[1];
        $this->assertInstanceOf(ArraySyntaxFixer::class, $arraySyntaxFixer);

        $configuration = $privatesAccessor->getPrivateProperty($arraySyntaxFixer, 'configuration');
        $this->assertSame([
            'syntax' => 'short',
        ], $configuration);

        /** @var VisibilityRequiredFixer $visibilityRequiredFixer */
        $visibilityRequiredFixer = $checkers[0];
        $this->assertInstanceOf(VisibilityRequiredFixer::class, $visibilityRequiredFixer);

        $configuration = $privatesAccessor->getPrivateProperty($visibilityRequiredFixer, 'configuration');
        $this->assertSame([
            'elements' => ['property'],
        ], $configuration);
    }
}
