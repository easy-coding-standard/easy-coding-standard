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
     * @param Sniff|FixerInterface $sourceClass
     * @param string $relativeFilePath
     */
    public function shouldSkipSourceClassAndFile($sourceClass, string $relativeFilePath): bool
    {
        foreach ($this->skipped as $skippedFile => $skippedSourceClass) {
            if ( ! $this->fileMatchesPattern($relativeFilePath, $skippedFile)) {
                continue;
            }

            foreach ($skippedSourceClass as $ignoredSourceClass) {
                if ($sourceClass instanceof $ignoredSourceClass) {
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
