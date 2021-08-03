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

use ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Method;
use ECSPrefix20210803\phpDocumentor\Reflection\DocBlockFactory;
use ECSPrefix20210803\phpDocumentor\Reflection\Types\ContextFactory;
/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 *
 * @internal
 */
final class ClassTagRetriever implements \ECSPrefix20210803\Prophecy\PhpDocumentor\MethodTagRetrieverInterface
{
    private $docBlockFactory;
    private $contextFactory;
    public function __construct()
    {
        $this->docBlockFactory = \ECSPrefix20210803\phpDocumentor\Reflection\DocBlockFactory::createInstance();
        $this->contextFactory = new \ECSPrefix20210803\phpDocumentor\Reflection\Types\ContextFactory();
    }
    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return Method[]
     */
    public function getTagList(\ReflectionClass $reflectionClass)
    {
        try {
            $phpdoc = $this->docBlockFactory->create($reflectionClass, $this->contextFactory->createFromReflector($reflectionClass));
            $methods = array();
            foreach ($phpdoc->getTagsByName('method') as $tag) {
                if ($tag instanceof \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Method) {
                    $methods[] = $tag;
                }
            }
            return $methods;
        } catch (\InvalidArgumentException $e) {
            return array();
        }
    }
}
