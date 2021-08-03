<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210803\Prophecy\PhpDocumentor;

use ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tag\MethodTag as LegacyMethodTag;
use ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Method;
/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 *
 * @internal
 */
interface MethodTagRetrieverInterface
{
    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return LegacyMethodTag[]|Method[]
     */
    public function getTagList(\ReflectionClass $reflectionClass);
}
