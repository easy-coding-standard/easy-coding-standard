<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Error;

final class ErrorFilter
{
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
}
