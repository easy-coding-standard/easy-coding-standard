<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symplify\SmartFileSystem\SmartFileInfo;

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
     * @var mixed[]
     */
    private $only = [];

    /**
     * @param mixed[] $skip
     * @param mixed[] $only
     * @param mixed[] $excludeFiles
     */
    public function __construct(array $skip, array $only, array $excludeFiles)
    {
        $this->categorizeSkipSettings($skip);
        $this->excludedFiles = $excludeFiles;
        $this->only = $only;
    }

    public function shouldSkipCodeAndFile(string $code, SmartFileInfo $smartFileInfo): bool
    {
        return $this->shouldSkipMatchingRuleAndFile($this->skippedCodes, $code, $smartFileInfo);
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
        $doesMatchOnly = $this->doesMatchOnly($checker, $smartFileInfo);
        if (is_bool($doesMatchOnly)) {
            return $doesMatchOnly;
        }

        return $this->doesMatchSkipped($checker, $smartFileInfo);
    }

    public function shouldSkipFileInfo(SmartFileInfo $smartFileInfo): bool
    {
        foreach ($this->excludedFiles as $excludedFile) {
            if ($this->doesFileMatchPattern($smartFileInfo, $excludedFile)) {
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
    private function shouldSkipMatchingRuleAndFile(array $rules, string $key, SmartFileInfo $smartFileInfo): bool
    {
        if (! array_key_exists($key, $rules)) {
            return false;
        }

        // skip regardless the path
        if ($rules[$key] === null) {
            return true;
        }

        return $this->doesFileMatchSkippedFiles($smartFileInfo, (array) $rules[$key]);
    }

    /**
     * @param FixerInterface|Sniff|string $checker
     */
    private function doesMatchOnly($checker, SmartFileInfo $smartFileInfo): ?bool
    {
        foreach ($this->only as $onlyClass => $onlyFiles) {
            if (! is_a($checker, $onlyClass, true)) {
                continue;
            }

            foreach ($onlyFiles as $onlyFile) {
                if ($this->doesFileMatchPattern($smartFileInfo, $onlyFile)) {
                    return false;
                }
            }

            return true;
        }

        return null;
    }

    /**
     * @param FixerInterface|Sniff|string $checker
     */
    private function doesMatchSkipped($checker, SmartFileInfo $smartFileInfo): bool
    {
        foreach ($this->skipped as $skippedClass => $skippedFiles) {
            if (! is_a($checker, $skippedClass, true)) {
                continue;
            }

            // skip everywhere
            if (! is_array($skippedFiles)) {
                return true;
            }

            if ($this->doesFileMatchSkippedFiles($smartFileInfo, $skippedFiles)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Supports both relative and absolute $file path.
     * They differ for PHP-CS-Fixer and PHP_CodeSniffer.
     */
    private function doesFileMatchPattern(SmartFileInfo $smartFileInfo, string $ignoredPath): bool
    {
        $ignoredPath = $this->normalizeForFnmatch($ignoredPath);

        return $smartFileInfo->endsWith($ignoredPath) || $smartFileInfo->doesFnmatch($ignoredPath);
    }

    /**
     * @param string[] $skippedFiles
     */
    private function doesFileMatchSkippedFiles(SmartFileInfo $smartFileInfo, array $skippedFiles): bool
    {
        foreach ($skippedFiles as $skippedFile) {
            if ($this->doesFileMatchPattern($smartFileInfo, $skippedFile)) {
                return true;
            }
        }

        return false;
    }

    /**
     * "value*" → "*value*"
     * "*value" → "*value*"
     */
    private function normalizeForFnmatch(string $path): string
    {
        // ends with *
        if (Strings::match($path, '#^[^*](.*?)\*$#')) {
            return '*' . $path;
        }
        // starts with *
        if (Strings::match($path, '#^\*(.*?)[^*]$#')) {
            return $path . '*';
        }
        return $path;
    }
}
