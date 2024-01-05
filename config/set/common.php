<?php

declare (strict_types=1);
namespace ECSPrefix202401;

use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;
return static function (ECSConfig $ecsConfig) : void {
    $ecsConfig->sets([SetList::ARRAY, SetList::COMMENTS, SetList::CONTROL_STRUCTURES, SetList::DOCBLOCK, SetList::NAMESPACES, SetList::PHPUNIT, SetList::SPACES, SetList::CLEAN_CODE]);
};
