<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Option;

return ECSConfig::configure()
    ->withRules([IndentationTypeFixer::class])
    ->withSpacing(indentation: Option::INDENTATION_TAB);
