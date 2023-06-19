<?php

declare (strict_types=1);
namespace ECSPrefix202306;

use ECSPrefix202306\Symplify\EasyCI\Config\EasyCIConfig;
return static function (EasyCIConfig $easyCIConfig) : void {
    $easyCIConfig->typesToSkip([]);
};
