<?php

declare (strict_types=1);
namespace ECSPrefix202303;

use ECSPrefix202303\Symplify\EasyCI\Config\EasyCIConfig;
return static function (EasyCIConfig $easyCIConfig) : void {
    $easyCIConfig->typesToSkip([]);
};
