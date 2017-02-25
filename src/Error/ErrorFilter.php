<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Error;

use Nette\Utils\Strings;
use Symfony\Component\Finder\Glob;
use Symplify\EasyCodingStandard\Configuration\ConfigurationNormalizer;

final class ErrorFilter
{
    /**
     * @var string[][]
     */
    private $ignoredErrors = [];

    /**
     * @var ConfigurationNormalizer
     */
    private $configurationNormalizer;

    public function __construct(ConfigurationNormalizer $configurationNormalizer)
    {
        $this->configurationNormalizer = $configurationNormalizer;
    }

    /**
     * @param string[] $ignoredErrors
     */
    public function setIgnoredErrors(array $ignoredErrors): void
    {
        $this->ignoredErrors = $this->configurationNormalizer->normalizeClassesConfiguration($ignoredErrors);
    }

    /**
     * @param Error[][] $errors
     * @return Error[][]
     */
    public function filterOutIgnoredErrors(array $errors): array
    {
        $nonIgnoredErrors = [];
        foreach ($errors as $file => $errorsForFile) {
            $nonIgnoredErrorsForFile = $this->filterOutIgnoredErrorsForFile($file, $errorsForFile);
            if (count($nonIgnoredErrorsForFile)) {
                $nonIgnoredErrors[$file] = $nonIgnoredErrorsForFile;
            }
        }

        return $nonIgnoredErrors;
    }

    /**
     * @param Error[][] $errors
     * @return Error[][]
     */
    public function filterOutFixableErrors(array $errors): array
    {
        $unfixableErrors = [];
        foreach ($errors as $file => $errorsForFile) {
            $unfixableErrorsForFile = $this->filterUnfixableErrorsForFile($errorsForFile);
            if (count($unfixableErrorsForFile)) {
                $unfixableErrors[$file] = $unfixableErrorsForFile;
            }
        }

        return $unfixableErrors;
    }

    /**
     * @param Error[] $errorsForFile
     * @return Error[]
     */
    private function filterUnfixableErrorsForFile(array $errorsForFile): array
    {
        $unfixableErrors = [];
        foreach ($errorsForFile as $error) {
            if ($error->isFixable()) {
                continue;
            }

            $unfixableErrors[] = $error;
        }

        return $unfixableErrors;
    }

    /**
     * @param string $file
     * @param Error[] $errorsForFile
     * @return Error[]
     */
    private function filterOutIgnoredErrorsForFile(string $file, array $errorsForFile): array
    {
        $unfixableErrors = [];
        foreach ($errorsForFile as $error) {
            foreach ($this->ignoredErrors as $ignoredFile => $ignoredSourceClasses) {
                if ($this->fileMatchesPattern($file , $ignoredFile) && $ignoredSourceClasses === []) {
                    return [];
                }

                if ($this->fileMatchesPattern($file, $ignoredFile)) {
                    foreach ($ignoredSourceClasses as $ignoredSourceClass) {
                        if ($error->getSourceClass() === $ignoredSourceClass) {
                            continue 3;
                        }
                    }
                }
            }

            $unfixableErrors[] = $error;
        }

        return $unfixableErrors;
    }

    private function fileMatchesPattern(string $file, string $ignoredPath): bool
    {
        return (bool) Strings::match($file, Glob::toRegex($ignoredPath));
    }
}
