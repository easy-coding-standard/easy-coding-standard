<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\FixerRunner\Tests\DependencyInjection;

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
final class FixerServiceRegistrationTest extends \Symplify\PackageBuilder\Testing\AbstractKernelTestCase
{
    /**
     * @var PrivatesAccessor
     */
    private $privatesAccessor;
    protected function setUp() : void
    {
        $this->privatesAccessor = new \Symplify\PackageBuilder\Reflection\PrivatesAccessor();
    }
    public function test() : void
    {
        $this->bootKernelWithConfigs(\Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel::class, [__DIR__ . '/FixerServiceRegistrationSource/easy-coding-standard.php']);
        $fixerFileProcessor = $this->getService(\Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor::class);
        $checkers = $fixerFileProcessor->getCheckers();
        $this->assertCount(2, $checkers);
        /** @var ArraySyntaxFixer $arraySyntaxFixer */
        $arraySyntaxFixer = $checkers[0];
        $this->assertInstanceOf(\PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer::class, $arraySyntaxFixer);
        $configuration = $this->privatesAccessor->getPrivateProperty($arraySyntaxFixer, 'configuration');
        $this->assertSame(['syntax' => 'short'], $configuration);
        /** @var VisibilityRequiredFixer $visibilityRequiredFixer */
        $visibilityRequiredFixer = $checkers[1];
        $this->assertInstanceOf(\PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer::class, $visibilityRequiredFixer);
        $configuration = $this->privatesAccessor->getPrivateProperty($visibilityRequiredFixer, 'configuration');
        $this->assertSame(['elements' => ['property']], $configuration);
    }
}
