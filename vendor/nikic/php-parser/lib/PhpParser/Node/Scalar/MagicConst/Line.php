<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\PhpParser\Node\Scalar\MagicConst;

use ConfigTransformer20210601\PhpParser\Node\Scalar\MagicConst;
class Line extends \ConfigTransformer20210601\PhpParser\Node\Scalar\MagicConst
{
    public function getName() : string
    {
        return '__LINE__';
    }
    public function getType() : string
    {
        return 'Scalar_MagicConst_Line';
    }
}
