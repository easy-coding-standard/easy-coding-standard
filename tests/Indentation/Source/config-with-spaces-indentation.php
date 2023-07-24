<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Option;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->rule(IndentationTypeFixer::class);
    $ecsConfig->indentation(Option::INDENTATION_SPACES);
};
