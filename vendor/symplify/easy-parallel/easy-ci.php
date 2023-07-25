<?php

declare (strict_types=1);
namespace ECSPrefix202307;

use ECSPrefix202307\Symplify\EasyParallel\Contract\SerializableInterface;
use ECSPrefix202307\Symplify\EasyCI\Config\EasyCIConfig;
return static function (EasyCIConfig $easyCIConfig) : void {
    $easyCIConfig->typesToSkip([SerializableInterface::class]);
};
