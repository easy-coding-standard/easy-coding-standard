<?php

namespace Symplify\EasyCodingStandard\Application;

use Symplify\EasyCodingStandard\Exception\Application\MissingCheckersForChangedFileException;
use Symplify\SmartFileSystem\SmartFileInfo;

final class AppliedCheckersCollector
{
    /**
     * @var array<string, class-string[]>
     */
    private $appliedCheckersByFile = [];

    /**
     * @return void
     * @param string $checker
     */
    public function addFileInfoAndChecker(SmartFileInfo $smartFileInfo, $checker)
    {
        $checker = (string) $checker;
        $this->appliedCheckersByFile[$smartFileInfo->getRealPath()][] = $checker;
    }

    /**
     * @return mixed[]
     */
    public function getAppliedCheckersPerFileInfo(SmartFileInfo $smartFileInfo)
    {
        $this->ensureFileHasAppliedCheckers($smartFileInfo);

        return $this->appliedCheckersByFile[$smartFileInfo->getRealPath()];
    }

    /**
     * @return void
     */
    private function ensureFileHasAppliedCheckers(SmartFileInfo $smartFileInfo)
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
