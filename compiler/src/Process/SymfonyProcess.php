<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Compiler\Process;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

final class SymfonyProcess
{
    /**
     * @param string[] $command
     */
    public function __construct(array $command, string $cwd, OutputInterface $output)
    {
        (new Process($command, $cwd, null, null, null))
            ->mustRun(static function (string $type, string $buffer) use ($output): void {
                $output->write($buffer);
            });
    }
}
