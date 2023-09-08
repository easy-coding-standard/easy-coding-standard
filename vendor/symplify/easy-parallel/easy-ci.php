<?php

declare (strict_types=1);
namespace ECSPrefix202309;

use ECSPrefix202309\Symplify\EasyParallel\Contract\SerializableInterface;
use ECSPrefix202309\Symplify\EasyCI\Config\EasyCIConfig;
return static function (EasyCIConfig $easyCIConfig) : void {
    $easyCIConfig->typesToSkip([SerializableInterface::class]);
};
