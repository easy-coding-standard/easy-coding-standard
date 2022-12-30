<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->skip([
        NoUnusedImportsFixer::class => null,
    ]);

    $ecsConfig->rule(NoUnusedImportsFixer::class);
};
