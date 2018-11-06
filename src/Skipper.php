<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

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

    public function shouldSkipCodeAndFile(string $code, SmartFileInfo $fileInfo): bool
    {
        return $this->shouldSkipMatchingRuleAndFile($this->skippedCodes, $code, $fileInfo);
    }

    public function shouldSkipMessageAndFile(string $message, SmartFileInfo $smartFileInfo): bool
    {
        return $this->shouldSkipMatchingRuleAndFile($this->skippedMessages, $message, $smartFileInfo);
    }

    /**
     * @param FixerInterface|Sniff|string $checker
     */
    public function shouldSkipCheckerAndFile($checker, SmartFileInfo $smartFileInfo): bool
    {
        foreach ($this->skipped as $skippedClass => $skippedFiles) {
            if (! is_a($checker, $skippedClass, true)) {
                continue;
            }

            if ($this->doesFileMatchSkippedFiles($smartFileInfo, $skippedFiles)) {
                return true;
            }
        }

        return false;
    }

    public function shouldSkipFileInfo(SmartFileInfo $fileInfosmartFileInfo): bool
    {
        foreach ($this->excludedFiles as $excludedFile) {
            if ($this->fileMatchesPattern($fileInfosmartFileInfo, $excludedFile)) {
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
    private function shouldSkipMatchingRuleAndFile(array $rules, string $key, SmartFileInfo $fileInfo): bool
    {
        if (! array_key_exists($key, $rules)) {
            return false;
        }

        // skip regardless the path
        if ($rules[$key] === null) {
            return true;
        }

        return $this->doesFileMatchSkippedFiles($fileInfo, (array) $rules[$key]);
    }

    /**
     * @param string[] $skippedFiles
     */
    private function doesFileMatchSkippedFiles(SmartFileInfo $fileInfo, array $skippedFiles): bool
    {
        foreach ($skippedFiles as $skippedFile) {
            if ($this->fileMatchesPattern($fileInfo, $skippedFile)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Supports both relative and absolute $file path.
     * They differ for PHP-CS-Fixer and PHP_CodeSniffer.
     */
    private function fileMatchesPattern(SmartFileInfo $smartFileInfo, string $ignoredPath): bool
    {
        return $smartFileInfo->endsWith($ignoredPath) || $smartFileInfo->fnmatches($ignoredPath);
    }
}
