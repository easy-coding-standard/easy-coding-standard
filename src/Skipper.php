<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symfony\Component\Finder\Glob;
use Symplify\EasyCodingStandard\Configuration\Parameter\ParameterProvider;
use Symplify\EasyCodingStandard\Validator\CheckerTypeValidator;

final class Skipper
{
    /**
     * @var string[][]
     */
    private $skipped = [];

    /**
     * @var string[][]
     */
    private $unusedSkipped = [];

    public function __construct(ParameterProvider $parameterProvider, CheckerTypeValidator $checkerTypeValidator)
    {
        $skipped = $parameterProvider->provide()['skip'] ?? [];
        $checkerTypeValidator->validate(array_keys($skipped));
        $this->skipped = $skipped;
        $this->unusedSkipped = $skipped;
    }

    /**
     * @param Sniff|FixerInterface|string $checker
     */
    public function shouldSkipCheckerAndFile($checker, string $relativeFilePath): bool
    {
        foreach ($this->skipped as $skippedClass => $skippedFiles) {
            if (! is_a($checker, $skippedClass, true)) {
                continue;
            }

            if ($this->doesFileMatchSkippedFiles($skippedClass, $relativeFilePath, $skippedFiles)) {
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
        string $relativeFilePath,
        array $skippedFiles
    ): bool {
        foreach ($skippedFiles as $key => $skippedFile) {
            if ($this->fileMatchesPattern($relativeFilePath, $skippedFile)) {
                unset($this->unusedSkipped[$skippedClass][$key]);

                return true;
            }
        }

        return false;
    }

    private function fileMatchesPattern(string $file, string $ignoredPath): bool
    {
        if ((bool) Strings::match($file, Glob::toRegex($ignoredPath))) {
            return true;
        }

        return Strings::endsWith($file, $ignoredPath);
    }
}
