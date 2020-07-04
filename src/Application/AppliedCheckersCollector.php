<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application;

use Symplify\EasyCodingStandard\Exception\Application\MissingCheckersForChangedFileException;
use Symplify\SmartFileSystem\SmartFileInfo;

final class AppliedCheckersCollector
{
    /**
     * @var string[][]
     */
    private $appliedCheckersByFile = [];

    public function addFileInfoAndChecker(SmartFileInfo $smartFileInfo, string $checker): void
    {
        $this->appliedCheckersByFile[$smartFileInfo->getRealPath()][] = $checker;
    }

    /**
     * @return string[]
     */
    public function getAppliedCheckersPerFileInfo(SmartFileInfo $smartFileInfo): array
    {
        $this->ensureFileHasAppliedCheckers($smartFileInfo);

        return $this->appliedCheckersByFile[$smartFileInfo->getRealPath()];
    }

    private function ensureFileHasAppliedCheckers(SmartFileInfo $smartFileInfo): void
    {
        if (isset($this->appliedCheckersByFile[$smartFileInfo->getRealPath()])) {
            return;
        }

        throw new MissingCheckersForChangedFileException(sprintf(
            'File "%s" was changed, but no responsible checkers were added to "%s".',
            $smartFileInfo->getRelativePathname(),
            self::class
        ));
    }
}
