<?php

declare (strict_types=1);
namespace ECSPrefix20210804\PhpParser\Node\Scalar\MagicConst;

use ECSPrefix20210804\PhpParser\Node\Scalar\MagicConst;
class Dir extends \ECSPrefix20210804\PhpParser\Node\Scalar\MagicConst
{
    public function getName() : string
    {
        return '__DIR__';
    }
    public function getType() : string
    {
        return 'Scalar_MagicConst_Dir';
    }
}
