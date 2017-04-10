<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Validator;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symplify\EasyCodingStandard\Exception\Validator\CheckerIsNotSupportedException;

final class CheckersConfigurationValidator
{
    /**
     * @var string[]
     */
    private $allowedCheckerTypes = [
        Sniff::class,
        FixerInterface::class
    ];

    /**
     * @param string[] $checkers
     */
    public function validate(array $checkers): void
    {
        // must be checker or sniff interface
        foreach ($checkers as $checker) {
            foreach ($this->allowedCheckerTypes as $allowedCheckerType) {
                if (is_a($checker, $allowedCheckerType, true)) {
                    continue 2;
                }
            }

            throw new CheckerIsNotSupportedException(
                'sdfasdfasdf'
            );
        }
    }
}
