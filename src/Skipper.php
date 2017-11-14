<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symplify\EasyCodingStandard\Validator\CheckerTypeValidator;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

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
     * @var string[][]
     */
    private $unusedSkipped = [];

    public function __construct(ParameterProvider $parameterProvider, CheckerTypeValidator $checkerTypeValidator)
    {
        $skipped = $parameterProvider->provide()['skip'] ?? [];
        $checkerTypeValidator->validate(array_keys($skipped), 'parameters > skip');
        $this->skipped = $skipped;
        $this->unusedSkipped = $skipped;

        $this->skippedCodes = $parameterProvider->provide()['skip_codes'] ?? [];
    }

    public function shouldSkipCodeAndFile(string $code, string $absoluteFilePath): bool
    {
        foreach ($this->skippedCodes as $index => $value) {
            if ($value === $code) {
                return true;
            }

            if ($index === $code && $this->doesFileMatchSkippedFiles($code, $absoluteFilePath, (array) $value)) {
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
        return $this->unusedSkipped;
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
    private function fileMatchesPattern(string $file, string $ignoredPath): bool
    {
        $file = str_replace('\\', '/', $file);

        if (Strings::endsWith($file, $ignoredPath)) {
            return true;
        }

        return fnmatch($ignoredPath, $file) || fnmatch($ignoredPath, '*/' . $file);
    }

    private function removeEmptyUnusedSkipped(string $skippedChecker): void
    {
        if ($this->unusedSkipped[$skippedChecker] === []) {
            unset($this->unusedSkipped[$skippedChecker]);
        }
    }
}
