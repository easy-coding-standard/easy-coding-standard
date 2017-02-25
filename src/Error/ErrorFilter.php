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

//    @todo
//    public function filterOutFixableErrors()
//    {
//
//    }

    /**
     * @param Error[] $errors
     * @return Error[]
     */
    public function filterOutIgnoredErrors(array $errors)
    {
        return array_values(
            array_filter($errors, $this->isErrorIgnored())
        );
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

    private function isErrorIgnored(): Closure
    {
        return function (Error $error): bool {
            foreach ($this->ignoredErrors as $ignored) {
                if (Strings::match($error->getMessage(), '#' . $ignored . '#') !== null) {
                    return false;
                }
            }

            return true;
        };
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
}
