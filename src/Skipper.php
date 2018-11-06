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
     * @var string[]
     */
    private $skippedCodes = [];

    /**
     * @var string[]
     */
    private $skippedMessages = [];

    /**
     * @var string[]
     */
    private $excludedFiles = [];

    /**
     * @param mixed[] $skip
     * @param mixed[] $excludeFiles
     */
    public function __construct(array $skip, array $excludeFiles)
    {
        $this->categorizeSkipSettings($skip);
        $this->excludedFiles = $excludeFiles;
    }

    public function shouldSkipCodeAndFile(string $code, string $filePath): bool
    {
        return $this->shouldSkipMatchingRuleAndFile($this->skippedCodes, $code, $filePath);
    }

    public function shouldSkipMessageAndFile(string $message, string $filePath): bool
    {
        return $this->shouldSkipMatchingRuleAndFile($this->skippedMessages, $message, $filePath);
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

            if ($this->doesFileMatchSkippedFiles($absoluteFilePath, $skippedFiles)) {
                return true;
            }
        }

        return false;
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
     * @param mixed[] $skipped
     */
    private function categorizeSkipSettings(array $skipped): void
    {
        foreach ($skipped as $key => $settings) {
            if (class_exists($key)) {
                $this->skipped[$key] = $settings;
            } elseif (class_exists((string) Strings::before($key, '.'))) {
                $this->skippedCodes[$key] = $settings;
            } else {
                $this->skippedMessages[$key] = $settings;
            }
        }
    }

    /**
     * @param string[]|null[] $rules
     */
    private function shouldSkipMatchingRuleAndFile(array $rules, string $key, string $filePath): bool
    {
        if (! array_key_exists($key, $rules)) {
            return false;
        }

        if ($rules[$key] === null) {
            return true;
        }

        return $this->doesFileMatchSkippedFiles($filePath, (array) $rules[$key]);
    }

    /**
     * @param string[] $skippedFiles
     */
    private function doesFileMatchSkippedFiles(string $absoluteFilePath, array $skippedFiles): bool
    {
        foreach ($skippedFiles as $skippedFile) {
            if ($this->fileMatchesPattern($absoluteFilePath, $skippedFile)) {
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
}
