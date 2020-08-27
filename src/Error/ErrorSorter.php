<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Error;

use Symplify\EasyCodingStandard\ValueObject\Error\CodingStandardError;

final class ErrorSorter
{
    /**
     * @param CodingStandardError[][] $errorMessages
     * @return CodingStandardError[][]
     */
    public function sortByFileAndLine(array $errorMessages): array
    {
        ksort($errorMessages);

        foreach ($errorMessages as $file => $errorMessagesForFile) {
            usort($errorMessagesForFile, function (CodingStandardError $first, CodingStandardError $second): int {
                return $first->getLine() <=> $second->getLine();
            });

            $errorMessages[$file] = $errorMessagesForFile;
        }

        return $errorMessages;
    }
}
