<?php declare(strict_types=1);

namespace Symplify\SniffRunner\Sniff\Finder;

use ReflectionClass;

final class SniffClassFilter
{
    public function filterOutAbstractAndNonPhpSniffClasses(array $originSniffClasses) : array
    {
        $finalSniffClasses = [];
        foreach ($originSniffClasses as $sniffClass) {
            if (!class_exists($sniffClass)) {
                continue;
            }

            if ($this->isAbstractClass($sniffClass)) {
                continue;
            }

            if (!$this->doesSniffSupportsPhp($sniffClass)) {
                continue;
            }

            $finalSniffClasses[] = $sniffClass;
        }

        return $finalSniffClasses;
    }

    private function isAbstractClass(string $className) : bool
    {
        return (new ReflectionClass($className))->isAbstract();
    }

    private function doesSniffSupportsPhp(string $className) : bool
    {
        $vars = get_class_vars($className);
        if (!isset($vars['supportedTokenizers'])) {
            return true;
        }

        if (in_array('PHP', $vars['supportedTokenizers'])) {
            return true;
        }

        return false;
    }
}
