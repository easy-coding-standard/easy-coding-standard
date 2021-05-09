<?php

namespace Symplify\EasyTesting\ValueObject;

final class SplitLine
{
    /**
     * @see https://regex101.com/r/8fuULy/1
     * @var string
     */
    const SPLIT_LINE_REGEX = "#\\-\\-\\-\\-\\-\r?\n#";
}
