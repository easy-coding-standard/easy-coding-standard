<?php

declare(strict_types=1);

use PHP_CodeSniffer\Standards\Squiz\Sniffs\Commenting\BlockCommentSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Files\FileExtensionSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Commenting\TodoSniff;

use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withRules([
        BlockCommentSniff::class,
        FileExtensionSniff::class,
        TodoSniff::class,
    ]);
