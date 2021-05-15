<?php

namespace Symplify\EasyCodingStandard\Application;

use Symplify\EasyCodingStandard\Exception\Application\MissingCheckersForChangedFileException;
use ECSPrefix20210515\Symplify\SmartFileSystem\SmartFileInfo;
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
    public function addFileInfoAndChecker(\ECSPrefix20210515\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo, $checker)
    {
        $checker = (string) $checker;
        $this->appliedCheckersByFile[$smartFileInfo->getRealPath()][] = $checker;
    }
    /**
     * @return mixed[]
     */
    public function getAppliedCheckersPerFileInfo(\ECSPrefix20210515\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
    {
        $this->ensureFileHasAppliedCheckers($smartFileInfo);
        return $this->appliedCheckersByFile[$smartFileInfo->getRealPath()];
    }
    /**
     * @return void
     */
    private function ensureFileHasAppliedCheckers(\ECSPrefix20210515\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
    {
        if (isset($this->appliedCheckersByFile[$smartFileInfo->getRealPath()])) {
            return;
        }
        throw new \Symplify\EasyCodingStandard\Exception\Application\MissingCheckersForChangedFileException(\sprintf('File "%s" was changed, but no responsible checkers were added to "%s".', $smartFileInfo->getRelativePathname(), self::class));
    }
}
