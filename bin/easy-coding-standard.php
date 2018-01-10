<?php declare(strict_types=1);

use Symplify\EasyCodingStandard\Exception\DeprecatedException;

throw new DeprecatedException(sprintf(
    'File %s is deprecated, use %s instead',
    __FILE__ ,
    __DIR__ . '/ecs'
));
