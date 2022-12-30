<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\DependencyInjection;

use PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\AssignmentInConditionSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\ForLoopWithTestFunctionCallSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\JumbledIncrementerSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\UnusedFunctionParameterSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\PropertyDeclarationSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Methods\MethodDeclarationSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP\CommentedOutCodeSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP\EvalSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Scope\StaticThisUsageSniff;
use Symplify\EasyCodingStandard\Kernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class ReportSniffClassesWarningsTest extends AbstractKernelTestCase
{
    public function testDefault(): void
    {
        $this->bootKernelWithConfigs(EasyCodingStandardKernel::class, []);

        $expectedClasses = [
            AssignmentInConditionSniff::class,
            PropertyDeclarationSniff::class,
            MethodDeclarationSniff::class,
            CommentedOutCodeSniff::class,
            UnusedFunctionParameterSniff::class,
        ];

        $parameterProvider = $this->getService(ParameterProvider::class);
        $providerClasses = $parameterProvider->provideArrayParameter(Option::REPORT_SNIFF_WARNINGS);

        $this->assertSame($expectedClasses, $providerClasses);
    }

    public function testCustom(): void
    {
        $this->bootKernelWithConfigs(
            EasyCodingStandardKernel::class,
            [__DIR__ . '/ReportSniffClassesWarnings/ecs-config.php']
        );

        $expectedClasses = [
            AssignmentInConditionSniff::class,
            PropertyDeclarationSniff::class,
            MethodDeclarationSniff::class,
            CommentedOutCodeSniff::class,
            UnusedFunctionParameterSniff::class,
            ForLoopWithTestFunctionCallSniff::class,
            JumbledIncrementerSniff::class,
            EvalSniff::class,
            StaticThisUsageSniff::class,
        ];

        $parameterProvider = $this->getService(ParameterProvider::class);
        $providerClasses = $parameterProvider->provideArrayParameter(Option::REPORT_SNIFF_WARNINGS);

        $this->assertSame($expectedClasses, $providerClasses);
    }
}
