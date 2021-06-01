<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\PhpParser\Node\Scalar\MagicConst;

use ConfigTransformer20210601\PhpParser\Node\Scalar\MagicConst;
class File extends \ConfigTransformer20210601\PhpParser\Node\Scalar\MagicConst
{
    public function getName() : string
    {
        return '__FILE__';
    }
    public function getType() : string
    {
        return 'Scalar_MagicConst_File';
    }
}
