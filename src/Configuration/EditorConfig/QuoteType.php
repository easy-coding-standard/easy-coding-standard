<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration\EditorConfig;

/**
 * @see https://github.com/jednano/codepainter#quote_type-single-double-auto
 */
enum QuoteType: string
{
    case Single = 'single';
    case Double = 'double';
    case Auto = 'auto';
}
