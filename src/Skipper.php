<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symfony\Component\Finder\Glob;
use Symplify\EasyCodingStandard\Configuration\Contract\Parameter\ParameterProviderInterface;
use Symplify\EasyCodingStandard\Contract\SkipperInterface;

final class Skipper implements SkipperInterface
{
    /**
     * @var string[][]
     */
    private $skipped = [];

    public function __construct(ParameterProviderInterface $parameterProvider)
    {
        $this->skipped = $parameterProvider->provide()['skip'] ?? [];
    }

    /**
     * @param Sniff|FixerInterface|string $checker
     * @param string $relativeFilePath
     */
    public function shouldSkipCheckerAndFile($checker, string $relativeFilePath): bool
    {
        foreach ($this->skipped as $skippedClass => $skippedFiles) {
            if (! is_a($checker, $skippedClass, true)) {
                continue;
            }

            if ($this->doesFileMatchSkippedFiles($relativeFilePath, $skippedFiles)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $relativeFilePath
     * @param string[] $skippedFiles
     */
    private function doesFileMatchSkippedFiles(string $relativeFilePath, array $skippedFiles): bool
    {
        foreach ($skippedFiles as $skippedFile) {
            if ($this->fileMatchesPattern($relativeFilePath, $skippedFile)) {
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
