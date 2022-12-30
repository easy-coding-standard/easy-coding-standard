<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Reporter;

use Symfony\Component\Console\Style\SymfonyStyle;

final class CheckerListReporter
{
    public function __construct(
        private SymfonyStyle $symfonyStyle
    ) {
    }

    /**
     * @param class-string[] $checkerClasses
     */
    public function report(array $checkerClasses, string $type): void
    {
        if ($checkerClasses === []) {
            return;
        }

        $sectionMessage = sprintf(
            '%d checker%s from %s:',
            count($checkerClasses),
            count($checkerClasses) === 1 ? '' : 's',
            $type
        );
        $this->symfonyStyle->section($sectionMessage);
        $this->symfonyStyle->listing($checkerClasses);
    }
}
