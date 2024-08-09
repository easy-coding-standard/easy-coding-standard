<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration\EditorConfig;

enum EndOfLine: string
{
    case Posix = 'lf';
    case Legacy = 'cr';
    case Windows = 'crlf';
}
