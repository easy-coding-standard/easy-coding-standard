<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Console\Output;

use ECSPrefix202208\Symfony\Component\Console\Command\Command;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult;
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
