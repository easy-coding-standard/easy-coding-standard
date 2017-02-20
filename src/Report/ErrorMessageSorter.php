<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Report;

final class ErrorMessageSorter
{
    /**
     * @param array[] $errorMessages
     */
    public function sortByFileAndLine(array $errorMessages): array
    {
        ksort($errorMessages);

        foreach ($errorMessages as $file => $errorMessagesForFile) {
            if (count($errorMessagesForFile) <= 1) {
                continue;
            }

            usort($errorMessagesForFile, function ($first, $second) {
                return ($first['line'] > $second['line']);
            });

            $errorMessages[$file] = $errorMessagesForFile;
        }

        return $errorMessages;
    }
}
