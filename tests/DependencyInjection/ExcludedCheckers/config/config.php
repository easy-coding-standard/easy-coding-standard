<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->skip([NoUnusedImportsFixer::class]);

    $ecsConfig->rule(NoUnusedImportsFixer::class);
};
