<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symfony\Component\Finder\SplFileInfo;

final class Skipper
{
    /**
     * @var string[][]
     */
    private $skipped = [];

    /**
     * @var string[]|null[]
     */
    private $skippedCodes = [];

    /**
     * @var string[][]
     */
    private $unusedSkipped = [];

    /**
     * @var string[]
     */
    private $excludedFiles = [];

    /**
     * @param mixed[] $skip
     * @param mixed[] $excludeFiles
     * @param mixed[] $rootSkip
     */
    public function __construct(array $skip, array $excludeFiles, array $rootSkip)
    {
        $this->unusedSkipped = $rootSkip;
        $this->filterToSkippedAndSkippedCodes($skip);
        $this->excludedFiles = $excludeFiles;
    }

    public function shouldSkipCodeAndFile(string $code, string $absoluteFilePath): bool
    {
        foreach ($this->skippedCodes as $index => $value) {
            if ($index !== $code) {
                continue;
            }

            // skip all
            if ($value === null) {
                unset($this->unusedSkipped[$index]);
                return true;
            }

            if ($this->doesFileMatchSkippedFiles($code, $absoluteFilePath, (array) $value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param FixerInterface|Sniff|string $checker
     */
    public function shouldSkipCheckerAndFile($checker, string $absoluteFilePath): bool
    {
        foreach ($this->skipped as $skippedClass => $skippedFiles) {
            if (! is_a($checker, $skippedClass, true)) {
                continue;
            }

            if ($this->doesFileMatchSkippedFiles($skippedClass, $absoluteFilePath, $skippedFiles)) {
                return true;
            }
        }

        return false;
    }

    public function removeFileFromUnused(string $relativePath): void
    {
        foreach ($this->unusedSkipped as $skippedChecker => $skippedFiles) {
            if (! is_array($skippedFiles)) {
                continue;
            }

            foreach ($skippedFiles as $key => $skippedFile) {
                if ($this->fileMatchesPattern($relativePath, $skippedFile)) {
                    unset($this->unusedSkipped[$skippedChecker][$key]);
                    $this->removeEmptyUnusedSkipped($skippedChecker);
                }
            }
        }
    }

    /**
     * @return mixed[][]
     */
    public function getUnusedSkipped(): array
    {
        // always remove global code skips: ref https://github.com/Symplify/Symplify/issues/778
        foreach ($this->skippedCodes as $index => $value) {
            // skip all
            if ($value === null) {
                unset($this->unusedSkipped[$index]);
            }
        }

        return $this->unusedSkipped;
    }

    public function shouldSkipFile(SplFileInfo $fileInfo): bool
    {
        foreach ($this->excludedFiles as $excludedFile) {
            if ($this->fileMatchesPattern($fileInfo->getRealPath(), $excludedFile)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string[] $skippedFiles
     */
    private function doesFileMatchSkippedFiles(
        string $skippedClass,
        string $absoluteFilePath,
        array $skippedFiles
    ): bool {
        foreach ($skippedFiles as $key => $skippedFile) {
            if ($this->fileMatchesPattern($absoluteFilePath, $skippedFile)) {
                unset($this->unusedSkipped[$skippedClass][$key]);

                return true;
            }
        }

        return false;
    }

    /**
     * Supports both relative and absolute $file path.
     * They differ for PHP-CS-Fixer and PHP_CodeSniffer.
     */
    private function fileMatchesPattern(string $absoluteFilePath, string $ignoredPath): bool
    {
        $absoluteFilePath = str_replace('\\', '/', $absoluteFilePath);

        if (Strings::endsWith($absoluteFilePath, $ignoredPath)) {
            return true;
        }

        return fnmatch($ignoredPath, $absoluteFilePath) || fnmatch('*/' . $ignoredPath, $absoluteFilePath);
    }

    private function removeEmptyUnusedSkipped(string $skippedChecker): void
    {
        if ($this->unusedSkipped[$skippedChecker] === []) {
            unset($this->unusedSkipped[$skippedChecker]);
        }
    }

    /**
     * @param mixed[] $skipped
     */
    private function filterToSkippedAndSkippedCodes(array $skipped): void
    {
        foreach ($skipped as $key => $settings) {
            if (Strings::contains($key, '.')) {
                $this->skippedCodes[$key] = $settings;
            } else {
                $this->skipped[$key] = $settings;
            }
        }
    }
}
