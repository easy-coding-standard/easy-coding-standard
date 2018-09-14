<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application;

use SplObjectStorage;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\EasyCodingStandard\Exception\Application\MissingCheckersForChangedFileException;
use function Safe\sprintf;

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

    public function addFileInfoAndChecker(SplFileInfo $fileInfo, string $checker): void
    {
        $appliedCheckersByFile = [$checker];
        if ($this->appliedCheckersByFile->contains($fileInfo)) {
            $appliedCheckersByFile = array_merge($this->appliedCheckersByFile[$fileInfo], [$checker]);
        }

        $this->appliedCheckersByFile->attach($fileInfo, $appliedCheckersByFile);
    }

    /**
     * @return string[]
     */
    public function getAppliedCheckersPerFileInfo(SplFileInfo $fileInfo): array
    {
        $this->ensureFileHasAppliedCheckers($fileInfo);

        return $this->appliedCheckersByFile[$fileInfo];
    }

    private function ensureFileHasAppliedCheckers(SplFileInfo $fileInfo): void
    {
        if (isset($this->appliedCheckersByFile[$fileInfo])) {
            return;
        }

        throw new MissingCheckersForChangedFileException(sprintf(
            'File "%s" was changed, but no responsible checkers were added to "%s".',
            $fileInfo->getRelativePathname(),
            self::class
        ));
    }
}
