<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\PhpParser\Node\Scalar\MagicConst;

use ConfigTransformer20210601\PhpParser\Node\Scalar\MagicConst;
class Trait_ extends \ConfigTransformer20210601\PhpParser\Node\Scalar\MagicConst
{
    public function getName() : string
    {
        return '__TRAIT__';
    }
    public function getType() : string
    {
        return 'Scalar_MagicConst_Trait';
    }
}
