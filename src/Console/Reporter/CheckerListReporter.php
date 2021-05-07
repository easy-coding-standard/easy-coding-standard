<?php

namespace Symplify\EasyCodingStandard\Console\Reporter;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use ECSPrefix20210507\Symfony\Component\Console\Style\SymfonyStyle;
final class CheckerListReporter
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @param \ECSPrefix20210507\Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle
     */
    public function __construct($symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }
    /**
     * @param FixerInterface[]|Sniff[] $checkers
     * @return void
     * @param string $type
     */
    public function report(array $checkers, $type)
    {
        if ($checkers === []) {
            return;
        }
        $checkerNames = \array_map(function ($checker) : string {
            return \get_class($checker);
        }, $checkers);
        $sectionMessage = \sprintf('%d checker%s from %s:', \count($checkers), \count($checkers) === 1 ? '' : 's', $type);
        $this->symfonyStyle->section($sectionMessage);
        \sort($checkerNames);
        $this->symfonyStyle->listing($checkerNames);
    }
}
