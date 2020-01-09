<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Compiler\Contract\Process;

use Symfony\Component\Process\Process;

interface ProcessInterface
{
    public function getProcess(): Process;
}
