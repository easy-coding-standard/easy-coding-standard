<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application;

use Symfony\Component\Finder\SplFileInfo;

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
     * @return string[][]
     */
    public function getAppliedCheckersByFile(): array
    {
        return $this->appliedCheckersByFile;
    }

    /**
     * @return string[]
     */
    public function getAppliedCheckersPerFile(string $filePath): array
    {
        return $this->appliedCheckersByFile[$filePath] ?? [];
    }
}
