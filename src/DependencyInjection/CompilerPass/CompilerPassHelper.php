<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\DependencyInjection\CompilerPass;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symplify\EasyCodingStandard\Config\ECSConfig;
final class CompilerPassHelper
{
    /**
     * @return array<class-string<Sniff|FixerInterface>>
     */
    public static function resolveCheckerClasses(ECSConfig $ecsConfig): array
    {
        return $ecsConfig->getCheckerClasses();
    }
    /**
     * @param class-string<Sniff|FixerInterface> $checkerClass
     */
    public static function removeCheckerFromContainer(ECSConfig $ecsConfig, string $checkerClass): void
    {
        $ecsConfig->removeChecker($checkerClass);
    }
}
