<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210803\Prophecy\Exception\Doubler;

use ECSPrefix20210803\Prophecy\Doubler\Generator\Node\ClassNode;
class ClassCreatorException extends \RuntimeException implements \ECSPrefix20210803\Prophecy\Exception\Doubler\DoublerException
{
    private $node;
    public function __construct($message, \ECSPrefix20210803\Prophecy\Doubler\Generator\Node\ClassNode $node)
    {
        parent::__construct($message);
        $this->node = $node;
    }
    public function getClassNode()
    {
        return $this->node;
    }
}
