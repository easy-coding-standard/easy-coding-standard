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
    private $ignoredErrors = [];

    public function __construct(ConfigurationNormalizer $configurationNormalizer)
    {
        $this->configurationNormalizer = $configurationNormalizer;
    }

    /**
     * @param string[] $ignoredErrors
     */
    public function setIgnoredErrors(array $ignoredErrors): void
    {
        $this->ignoredErrors = $this->configurationNormalizer->normalizeClassesConfiguration(
            $ignoredErrors
        );
    }

    /**
     * @param Sniff|FixerInterface $sourceClass
     * @param string $relativeFilePath
     */
    public function shouldSkipSourceClassAndFile($sourceClass, string $relativeFilePath) : bool
    {
        foreach ($this->ignoredErrors as $ignoredFile => $ignoredSourceClasses) {
            if ($this->fileMatchesPattern($relativeFilePath, $ignoredFile)) {
                if ($ignoredSourceClasses === []) {
                    return true;
                }

                foreach ($ignoredSourceClasses as $ignoredSourceClass) {
                    if ($sourceClass instanceof $ignoredSourceClass) {
                        return true;
                    }
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
