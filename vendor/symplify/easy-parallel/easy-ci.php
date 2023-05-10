<?php

declare (strict_types=1);
namespace ECSPrefix202305;

use ECSPrefix202305\Symplify\EasyParallel\Contract\SerializableInterface;
use ECSPrefix202305\Symplify\EasyCI\Config\EasyCIConfig;
return static function (EasyCIConfig $easyCIConfig) : void {
    $easyCIConfig->typesToSkip([SerializableInterface::class]);
};
