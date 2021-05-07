<?php

namespace Symplify\EasyTesting\ValueObject;

final class Prefix
{
    /**
     * @var string
     * @see https://regex101.com/r/g4ozU6/1
     */
    const SKIP_PREFIX_REGEX = '#^(skip|keep)#i';
}
