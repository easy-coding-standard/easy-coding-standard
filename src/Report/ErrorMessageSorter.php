<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Report;

use Symplify\EasyCodingStandard\Report\Error\Error;

final class ErrorMessageSorter
{
    /**
     * @param Error[] $errorMessages
     * @return Error[]
     */
    public function sortByFileAndLine(array $errorMessages): array
    {
        ksort($errorMessages);

        foreach ($errorMessages as $file => $errorMessagesForFile) {
            if (count($errorMessagesForFile) <= 1) {
                continue;
            }

            usort($errorMessagesForFile, function (Error $first, Error $second) {
                return ($first->getLine() > $second->getLine());
            });

            $errorMessages[$file] = $errorMessagesForFile;
        }

        return $errorMessages;
    }
}
