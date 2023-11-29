<?php

declare (strict_types=1);
namespace ECSPrefix202311;

use ECSPrefix202311\Symplify\EasyParallel\Contract\SerializableInterface;
use ECSPrefix202311\Symplify\EasyCI\Config\EasyCIConfig;
return static function (EasyCIConfig $easyCIConfig) : void {
    $easyCIConfig->typesToSkip([SerializableInterface::class]);
};
