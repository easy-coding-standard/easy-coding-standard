<?php declare(strict_types = 1);

namespace Symplify\EasyCodingStandard\Contract\Application;

use SplFileInfo;
use Symplify\EasyCodingStandard\Application\Command\RunCommand;

interface FileProcessorInterface
{
    public function setupWithCommand(RunCommand $runCommand): void;

    public function processFile(SplFileInfo $file, bool $isFixer): void;
}
