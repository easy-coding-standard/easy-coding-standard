<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;

final class CurrentCheckerProvider
{
    /**
     * @var string|null
     */
    private $checker;

    /**
     * @param string|Sniff|FixerInterface $checker
     */
    public function setChecker($checker): void
    {
        $this->checker = is_object($checker) ? get_class($checker) : $checker;
    }

    public function getChecker(): ?string
    {
        return $this->checker;
    }
}
