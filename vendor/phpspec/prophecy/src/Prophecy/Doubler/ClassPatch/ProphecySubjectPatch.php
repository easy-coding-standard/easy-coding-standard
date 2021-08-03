<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210803\Prophecy\Doubler\ClassPatch;

use ECSPrefix20210803\Prophecy\Doubler\Generator\Node\ArgumentTypeNode;
use ECSPrefix20210803\Prophecy\Doubler\Generator\Node\ClassNode;
use ECSPrefix20210803\Prophecy\Doubler\Generator\Node\MethodNode;
use ECSPrefix20210803\Prophecy\Doubler\Generator\Node\ArgumentNode;
use ECSPrefix20210803\Prophecy\Doubler\Generator\Node\ReturnTypeNode;
/**
 * Add Prophecy functionality to the double.
 * This is a core class patch for Prophecy.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class ProphecySubjectPatch implements \ECSPrefix20210803\Prophecy\Doubler\ClassPatch\ClassPatchInterface
{
    /**
     * Always returns true.
     *
     * @param ClassNode $node
     *
     * @return bool
     */
    public function supports(\ECSPrefix20210803\Prophecy\Doubler\Generator\Node\ClassNode $node)
    {
        return \true;
    }
    /**
     * Apply Prophecy functionality to class node.
     *
     * @param ClassNode $node
     */
    public function apply(\ECSPrefix20210803\Prophecy\Doubler\Generator\Node\ClassNode $node)
    {
        $node->addInterface('ECSPrefix20210803\\Prophecy\\Prophecy\\ProphecySubjectInterface');
        $node->addProperty('objectProphecyClosure', 'private');
        foreach ($node->getMethods() as $name => $method) {
            if ('__construct' === \strtolower($name)) {
                continue;
            }
            if ($method->getReturnTypeNode()->isVoid()) {
                $method->setCode('$this->getProphecy()->makeProphecyMethodCall(__FUNCTION__, func_get_args());');
            } else {
                $method->setCode('return $this->getProphecy()->makeProphecyMethodCall(__FUNCTION__, func_get_args());');
            }
        }
        $prophecySetter = new \ECSPrefix20210803\Prophecy\Doubler\Generator\Node\MethodNode('setProphecy');
        $prophecyArgument = new \ECSPrefix20210803\Prophecy\Doubler\Generator\Node\ArgumentNode('prophecy');
        $prophecyArgument->setTypeNode(new \ECSPrefix20210803\Prophecy\Doubler\Generator\Node\ArgumentTypeNode('ECSPrefix20210803\\Prophecy\\Prophecy\\ProphecyInterface'));
        $prophecySetter->addArgument($prophecyArgument);
        $prophecySetter->setCode(<<<PHP
if (null === \$this->objectProphecyClosure) {
    \$this->objectProphecyClosure = static function () use (\$prophecy) {
        return \$prophecy;
    };
}
PHP
);
        $prophecyGetter = new \ECSPrefix20210803\Prophecy\Doubler\Generator\Node\MethodNode('getProphecy');
        $prophecyGetter->setCode('return \\call_user_func($this->objectProphecyClosure);');
        if ($node->hasMethod('__call')) {
            $__call = $node->getMethod('__call');
        } else {
            $__call = new \ECSPrefix20210803\Prophecy\Doubler\Generator\Node\MethodNode('__call');
            $__call->addArgument(new \ECSPrefix20210803\Prophecy\Doubler\Generator\Node\ArgumentNode('name'));
            $__call->addArgument(new \ECSPrefix20210803\Prophecy\Doubler\Generator\Node\ArgumentNode('arguments'));
            $node->addMethod($__call, \true);
        }
        $__call->setCode(<<<PHP
throw new \\Prophecy\\Exception\\Doubler\\MethodNotFoundException(
    sprintf('Method `%s::%s()` not found.', get_class(\$this), func_get_arg(0)),
    get_class(\$this), func_get_arg(0)
);
PHP
);
        $node->addMethod($prophecySetter, \true);
        $node->addMethod($prophecyGetter, \true);
    }
    /**
     * Returns patch priority, which determines when patch will be applied.
     *
     * @return int Priority number (higher - earlier)
     */
    public function getPriority()
    {
        return 0;
    }
}
