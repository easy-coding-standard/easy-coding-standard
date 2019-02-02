<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application;

use SplObjectStorage;
use Symplify\EasyCodingStandard\Exception\Application\MissingCheckersForChangedFileException;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class AppliedCheckersCollector
{
    /**
     * @var  string[][]|SplObjectStorage
     */
    private $appliedCheckersByFile;

    public function __construct()
    {
        $this->appliedCheckersByFile = new SplObjectStorage();
    }

    public function addFileInfoAndChecker(SmartFileInfo $smartFileInfo, string $checker): void
    {
        $appliedCheckersByFile = [$checker];
        if ($this->appliedCheckersByFile->contains($smartFileInfo)) {
            $appliedCheckersByFile = array_merge($this->appliedCheckersByFile[$smartFileInfo], [$checker]);
        }

        $this->appliedCheckersByFile->attach($smartFileInfo, $appliedCheckersByFile);
    }

    /**
     * @return string[]
     */
    public function getAppliedCheckersPerFileInfo(SmartFileInfo $smartFileInfo): array
    {
        $this->ensureFileHasAppliedCheckers($smartFileInfo);

        return $this->appliedCheckersByFile[$smartFileInfo];
    }

    private function ensureFileHasAppliedCheckers(SmartFileInfo $smartFileInfo): void
    {
        if (isset($this->appliedCheckersByFile[$smartFileInfo])) {
            return;
        }

        throw new MissingCheckersForChangedFileException(sprintf(
            'File "%s" was changed, but no responsible checkers were added to "%s".',
            $smartFileInfo->getRelativePathname(),
            self::class
        ));
    }
}
