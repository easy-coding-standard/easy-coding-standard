<?php

declare (strict_types=1);
namespace ECSPrefix202302;

use ECSPrefix202302\Symplify\EasyParallel\Contract\SerializableInterface;
use ECSPrefix202302\Symplify\EasyCI\Config\EasyCIConfig;
return static function (EasyCIConfig $easyCIConfig) : void {
    $easyCIConfig->typesToSkip([SerializableInterface::class]);
};
