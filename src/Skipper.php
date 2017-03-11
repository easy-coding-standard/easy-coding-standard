<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symfony\Component\Finder\Glob;
use Symplify\EasyCodingStandard\Configuration\ConfigurationNormalizer;

final class Skipper
{
    /**
     * @var ConfigurationNormalizer
     */
    private $configurationNormalizer;

    /**
     * @var string[][]
     */
    private $skipped = [];

    public function __construct(ConfigurationNormalizer $configurationNormalizer)
    {
        $this->configurationNormalizer = $configurationNormalizer;
    }

    /**
     * @param string[] $skipped
     */
    public function setSkipped(array $skipped): void
    {
        $this->skipped = $this->configurationNormalizer->normalizeSkipperConfiguration(
            $skipped
        );
    }

    /**
     * @param Sniff|FixerInterface $checker
     * @param string $relativeFilePath
     */
    public function shouldSkipCheckerAndFile($checker, string $relativeFilePath): bool
    {
        foreach ($this->skipped as $skippedFile => $skippedCheckerClasses) {
            if (! $this->fileMatchesPattern($relativeFilePath, $skippedFile)) {
                continue;
            }

            foreach ($skippedCheckerClasses as $ignoredCheckerClass) {
                if (is_a($checker, $ignoredCheckerClass, true)) {
                    return true;
                }
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
