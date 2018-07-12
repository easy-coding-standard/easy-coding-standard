<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\Finder;

use ReflectionClass;

final class FixerClassFilter
{
    /**
     * @param string[] $originFixerClasses
     * @return string[]
     */
    public function filterOutAbstractAndNonPhpFixerClasses(array $originFixerClasses): array
    {
        $finalFixerClasses = [];
        foreach ($originFixerClasses as $sniffClass) {
            if (! class_exists($sniffClass)) {
                continue;
            }

            if ($this->isAbstractClass($sniffClass)) {
                continue;
            }

            if (! $this->doesFixerSupportPhp($sniffClass)) {
                continue;
            }

            $finalFixerClasses[] = $sniffClass;
        }

        return $finalFixerClasses;
    }

    private function isAbstractClass(string $className): bool
    {
        return (new ReflectionClass($className))->isAbstract();
    }

    private function doesFixerSupportPhp(string $className): bool
    {
        $vars = get_class_vars($className);
        if (! isset($vars['supportedTokenizers'])) {
            return true;
        }

        return in_array('PHP', $vars['supportedTokenizers'], true);
    }
}
