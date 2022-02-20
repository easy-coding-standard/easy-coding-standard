<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Console\Output;

use ECSPrefix20220220\Symfony\Component\Console\Command\Command;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult;
final class ExitCodeResolver
{
    public function resolve(\Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult $errorAndDiffResult, \Symplify\EasyCodingStandard\ValueObject\Configuration $configuration) : int
    {
        if ($errorAndDiffResult->getErrorCount() === 0 && $errorAndDiffResult->getFileDiffsCount() === 0) {
            return \ECSPrefix20220220\Symfony\Component\Console\Command\Command::SUCCESS;
        }
        if ($configuration->isFixer()) {
            return $errorAndDiffResult->getErrorCount() === 0 ? \ECSPrefix20220220\Symfony\Component\Console\Command\Command::SUCCESS : \ECSPrefix20220220\Symfony\Component\Console\Command\Command::FAILURE;
        }
        return $errorAndDiffResult->getErrorCount() !== 0 || $errorAndDiffResult->getFileDiffsCount() !== 0 ? \ECSPrefix20220220\Symfony\Component\Console\Command\Command::FAILURE : \ECSPrefix20220220\Symfony\Component\Console\Command\Command::SUCCESS;
    }
}
