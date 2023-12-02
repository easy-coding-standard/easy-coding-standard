<?php

declare (strict_types=1);
namespace ECSPrefix202312;

use ECSPrefix202312\Symplify\EasyParallel\Contract\SerializableInterface;
use ECSPrefix202312\Symplify\EasyCI\Config\EasyCIConfig;
return static function (EasyCIConfig $easyCIConfig) : void {
    $easyCIConfig->typesToSkip([SerializableInterface::class]);
};
