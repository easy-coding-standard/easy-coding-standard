<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Reporter;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class CheckerListReporter
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }

    /**
     * @param FixerInterface[]|Sniff[]|string[] $checkers
     */
    public function report(array $checkers, string $type): void
    {
        if ($checkers === []) {
            return;
        }

        $checkerNames = array_map(function ($fixer): string {
            return is_string($fixer) ? $fixer : get_class($fixer);
        }, $checkers);

        $sectionMessage = sprintf('%d checker%s from %s:', count($checkers), count($checkers) === 1 ? '' : 's', $type);
        $this->symfonyStyle->section($sectionMessage);

        sort($checkerNames);
        $this->symfonyStyle->listing($checkerNames);
    }
}
