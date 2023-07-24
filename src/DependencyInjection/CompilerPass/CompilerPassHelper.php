<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection\CompilerPass;

use Illuminate\Container\Container;
use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;

final class CompilerPassHelper
{
    /**
     * @return string[]
     */
    public static function resolveCheckerClasses(Container $container): array
    {
        $serviceTypes = array_keys($container->getBindings());

        $checkerTypes = array_filter($serviceTypes, function (string $serviceType) {
            if (is_a($serviceType, FixerInterface::class, true)) {
                return true;
            }

            return is_a($serviceType, Sniff::class, true);
        });

        return $checkerTypes;
    }

    public static function removeCheckerFromContainer(COntainer $container, string $checkerClass): void
    {
        // remove instance
        $container->offsetUnset($checkerClass);

        $tagsReflectionProperty = new \ReflectionProperty($container, 'tags');
        $tags = $tagsReflectionProperty->getValue($container);

        // nothing to remove
        if ($tags === []) {
            return;
        }

        // remove from tags
        $checkerTagClasses = [FixerInterface::class, Sniff::class];

        foreach ($checkerTagClasses as $checkerTagClass) {
            foreach ($tags[$checkerTagClass] ?? [] as $key => $class) {
                if ($class !== $checkerClass) {
                    continue;
                }

                unset($tags[$checkerTagClass][$key]);
            }
        }

        // update value
        $tagsReflectionProperty->setValue($container, $tags);
    }
}
