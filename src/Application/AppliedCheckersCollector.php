<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application;

use Symplify\EasyCodingStandard\Exception\Application\MissingCheckersForChangedFileException;

final class AppliedCheckersCollector
{
    /**
     * @var string[][]
     */
    private $appliedCheckersByFile = [];

    public function addFileAndChecker(string $filePath, string $checker): void
    {
        $this->appliedCheckersByFile[$filePath][] = $checker;
    }

    /**
     * @return string[]
     */
    public function getAppliedCheckersPerFile(string $filePath): array
    {
        $this->ensureFileHasAppliedCheckers($filePath);

        return $this->appliedCheckersByFile[$filePath];
    }

    private function ensureFileHasAppliedCheckers(string $filePath): void
    {
        if (isset($this->appliedCheckersByFile[$filePath])) {
            return;
        }

        throw new MissingCheckersForChangedFileException(sprintf(
            'File "%s" was changed, but no responsible checkers were added to "%s".',
            $filePath,
            self::class
        ));
    }
}
