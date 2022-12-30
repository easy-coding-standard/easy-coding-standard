<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\FixerRunner\DependencyInjection;

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\Kernel\EasyCodingStandardKernel;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class FixerServiceRegistrationTest extends AbstractKernelTestCase
{
    private PrivatesAccessor $privatesAccessor;

    protected function setUp(): void
    {
        $this->privatesAccessor = new PrivatesAccessor();
    }

    public function test(): void
    {
        $this->bootKernelWithConfigs(
            EasyCodingStandardKernel::class,
            [__DIR__ . '/FixerServiceRegistrationSource/easy-coding-standard.php']
        );

        $fixerFileProcessor = $this->getService(FixerFileProcessor::class);

        $checkers = $fixerFileProcessor->getCheckers();
        $this->assertCount(2, $checkers);

        /** @var ArraySyntaxFixer $arraySyntaxFixer */
        $arraySyntaxFixer = $checkers[1];
        $this->assertInstanceOf(ArraySyntaxFixer::class, $arraySyntaxFixer);

        $configuration = $this->privatesAccessor->getPrivateProperty($arraySyntaxFixer, 'configuration');
        $this->assertSame([
            'syntax' => 'short',
        ], $configuration);

        /** @var VisibilityRequiredFixer $visibilityRequiredFixer */
        $visibilityRequiredFixer = $checkers[0];
        $this->assertInstanceOf(VisibilityRequiredFixer::class, $visibilityRequiredFixer);

        $configuration = $this->privatesAccessor->getPrivateProperty($visibilityRequiredFixer, 'configuration');
        $this->assertSame([
            'elements' => ['property'],
        ], $configuration);
    }
}
