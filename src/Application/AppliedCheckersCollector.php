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
     * @param \Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo
     * @param string $checker
     */
    public function addFileInfoAndChecker($smartFileInfo, $checker)
    {
        $this->appliedCheckersByFile[$smartFileInfo->getRealPath()][] = $checker;
    }
    /**
     * @return mixed[]
     * @param \Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo
     */
    public function getAppliedCheckersPerFileInfo($smartFileInfo)
    {
        $this->ensureFileHasAppliedCheckers($smartFileInfo);
        return $this->appliedCheckersByFile[$smartFileInfo->getRealPath()];
    }
    /**
     * @return void
     * @param \Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo
     */
    private function ensureFileHasAppliedCheckers($smartFileInfo)
    {
        if (isset($this->appliedCheckersByFile[$smartFileInfo->getRealPath()])) {
            return;
        }
        throw new MissingCheckersForChangedFileException(\sprintf('File "%s" was changed, but no responsible checkers were added to "%s".', $smartFileInfo->getRelativePathname(), self::class));
    }
}
