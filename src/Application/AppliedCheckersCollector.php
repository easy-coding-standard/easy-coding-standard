<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Application;

use Symplify\EasyCodingStandard\Exception\Application\MissingCheckersForChangedFileException;
use ECSPrefix20210519\Symplify\SmartFileSystem\SmartFileInfo;
final class AppliedCheckersCollector
{
    /**
     * @var array<string, class-string[]>
     */
    private $appliedCheckersByFile = [];
    /**
     * @return void
     */
    public function addFileInfoAndChecker(\ECSPrefix20210519\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo, string $checker)
    {
        $this->appliedCheckersByFile[$smartFileInfo->getRealPath()][] = $checker;
    }
    /**
     * @return class-string[]
     */
    public function getAppliedCheckersPerFileInfo(\ECSPrefix20210519\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo) : array
    {
        $this->ensureFileHasAppliedCheckers($smartFileInfo);
        return $this->appliedCheckersByFile[$smartFileInfo->getRealPath()];
    }
    /**
     * @return void
     */
    private function ensureFileHasAppliedCheckers(\ECSPrefix20210519\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
    {
        if (isset($this->appliedCheckersByFile[$smartFileInfo->getRealPath()])) {
            return;
        }
        throw new \Symplify\EasyCodingStandard\Exception\Application\MissingCheckersForChangedFileException(\sprintf('File "%s" was changed, but no responsible checkers were added to "%s".', $smartFileInfo->getRelativePathname(), self::class));
    }
}
