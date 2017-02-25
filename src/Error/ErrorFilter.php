<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Error;

use Closure;
use Nette\Utils\Strings;

final class ErrorFilter
{
    /**
     * @var string[]
     */
    private $ignoredErrors = [];

    /**
     * @param string[] $ignoredErrors
     */
    public function setIgnoredErrors(array $ignoredErrors)
    {
        $this->ignoredErrors = $ignoredErrors;
    }

    /**
     * @param Error[][] $errors
     * @return Error[][]
     */
    public function filterOutIgnoredErrors(array $errors): array
    {
        $nonIgnoredErrors = [];
        foreach ($errors as $file => $errorsForFile) {
            $nonIgnoredErrorsForFile = $this->filterOutIgnoredErrorsForFile($errorsForFile);
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
     * @param Error[] $errorsForFile
     * @return Error[]
     */
    private function filterOutIgnoredErrorsForFile(array $errorsForFile): array
    {
        $unfixableErrors = [];
        foreach ($errorsForFile as $error) {
            foreach ($this->ignoredErrors as $ignored) {
                if (Strings::match($error->getMessage(), '#' . $ignored . '#')) {
                    continue 2;
                }
            }

            $unfixableErrors[] = $error;
        }

        return $unfixableErrors;
    }
}
