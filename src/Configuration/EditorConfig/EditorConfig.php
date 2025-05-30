<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration\EditorConfig;

/**
 * @see https://github.com/editorconfig/editorconfig/wiki/EditorConfig-Properties
 */
class EditorConfig
{
    public function __construct(
        public readonly ?string $indentStyle,
        public readonly ?string $endOfLine,
        public readonly ?bool $trimTrailingWhitespace,
        public readonly ?bool $insertFinalNewline,
        public readonly ?int $maxLineLength,
        public readonly ?string $quoteType
    ) {
    }
}
