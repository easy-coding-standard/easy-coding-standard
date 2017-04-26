<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;

final class CheckerFilter
{
    /**
     * @param mixed[][]
     * @return mixed[][]
     */
    public function filterSniffs(array $checkers): array
    {
        return $this->filterClassesByType($checkers, Sniff::class);
    }

    /**
     * @param mixed[][] $checkers
     * @return mixed[][]
     */
    public function filterFixers(array $checkers): array
    {
        return $this->filterClassesByType($checkers, FixerInterface::class);
    }

    /**
     * @param mixed[][] $classes
     * @param string $type
     * @return mixed[][]
     */
    private function filterClassesByType(array $classes, string $type): array
    {
        return array_filter($classes, function ($class) use ($type) {
            return is_a($class, $type, true);
        }, ARRAY_FILTER_USE_KEY);
    }
}
