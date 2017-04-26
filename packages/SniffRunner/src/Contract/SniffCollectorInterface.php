<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Contract;

use PHP_CodeSniffer\Sniffs\Sniff;

interface SniffCollectorInterface
{
    public function addSniff(Sniff $sniff): void;
}
