<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\PhpParser\Node\Scalar\MagicConst;

use ConfigTransformer20210601\PhpParser\Node\Scalar\MagicConst;
class Class_ extends \ConfigTransformer20210601\PhpParser\Node\Scalar\MagicConst
{
    public function getName() : string
    {
        return '__CLASS__';
    }
    public function getType() : string
    {
        return 'Scalar_MagicConst_Class';
    }
}
