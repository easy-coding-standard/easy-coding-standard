<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Validator;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symplify\EasyCodingStandard\Exception\Validator\CheckerIsNotSupportedException;

final class CheckerTypeValidator
{
    /**
     * @var string[]
     */
    private $allowedCheckerTypes = [Sniff::class, FixerInterface::class];

    /**
     * @param string[] $checkers
     */
    public function validate(array $checkers): void
    {
        foreach ($checkers as $checker) {
            if ($this->isCheckerSupported($checker)) {
                continue;
            }

            throw new CheckerIsNotSupportedException(sprintf(
                'Checker "%s" is not supported. Use class that implements any of %s.',
                $checker,
                implode(' or ', $this->allowedCheckerTypes)
            ));
        }
    }

    private function isCheckerSupported(string $checker): bool
    {
        foreach ($this->allowedCheckerTypes as $allowedCheckerType) {
            if (is_a($checker, $allowedCheckerType, true)) {
                return true;
            }
        }

        return false;
    }
}
