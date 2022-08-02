<?php

declare (strict_types=1);
namespace ECSPrefix202208;

use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\NamespaceNotation\SingleBlankLineBeforeNamespaceFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
return static function (ECSConfig $ecsConfig) : void {
    $ecsConfig->rules([NoUnusedImportsFixer::class, OrderedImportsFixer::class, SingleBlankLineBeforeNamespaceFixer::class]);
};
