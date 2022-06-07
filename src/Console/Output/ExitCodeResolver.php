<?php

declare (strict_types=1);
namespace ECSPrefix20220607\Symplify\EasyCodingStandard\Console\Output;

use ECSPrefix20220607\Symfony\Component\Console\Command\Command;
use ECSPrefix20220607\Symplify\EasyCodingStandard\ValueObject\Configuration;
use ECSPrefix20220607\Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult;
final class ExitCodeResolver
{
    public function resolve(ErrorAndDiffResult $errorAndDiffResult, Configuration $configuration) : int
    {
        if ($errorAndDiffResult->getErrorCount() === 0 && $errorAndDiffResult->getFileDiffsCount() === 0) {
            return Command::SUCCESS;
        }
        if ($configuration->isFixer()) {
            return $errorAndDiffResult->getErrorCount() === 0 ? Command::SUCCESS : Command::FAILURE;
        }
        return $errorAndDiffResult->getErrorCount() !== 0 || $errorAndDiffResult->getFileDiffsCount() !== 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
