<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\PHP_CodeSniffer;

use PHP_CodeSniffer\Fixer;

final class FixerFactory
{
    public function create(): Fixer
    {
        $fixer = new Fixer();
        $fixer->enabled = true;

        return $fixer;
    }
}
