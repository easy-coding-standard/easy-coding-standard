<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration\EditorConfig;

/**
 * @see https://github.com/editorconfig/editorconfig/wiki/EditorConfig-Properties
 */
class EditorConfig
{
    public function __construct(
        public readonly ?IndentStyle $indentStyle,
        public readonly ?EndOfLine $endOfLine,
        public readonly ?bool $trimTrailingWhitespace,
        public readonly ?bool $insertFinalNewline,
        public readonly ?int $maxLineLength,
        public readonly ?QuoteType $quoteType
    ) {
    }
}
