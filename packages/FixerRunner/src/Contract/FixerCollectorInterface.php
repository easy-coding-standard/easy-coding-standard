<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\Contract;

use PhpCsFixer\Fixer\FixerInterface;

interface FixerCollectorInterface
{
    public function addFixer(FixerInterface $fixer): void;
}