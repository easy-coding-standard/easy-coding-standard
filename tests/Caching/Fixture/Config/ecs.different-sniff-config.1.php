<?php

declare(strict_types=1);

use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withConfiguredRule(LineLengthSniff::class, [
        'lineLimit' => 120,
    ]);
