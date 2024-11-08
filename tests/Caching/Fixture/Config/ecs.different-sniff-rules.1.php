<?php

declare(strict_types=1);

use PHP_CodeSniffer\Standards\Squiz\Sniffs\Commenting\BlockCommentSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Files\FileExtensionSniff;

use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withRules([
        BlockCommentSniff::class,
        FileExtensionSniff::class,
    ]);
