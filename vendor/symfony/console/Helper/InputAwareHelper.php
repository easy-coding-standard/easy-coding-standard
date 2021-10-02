<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20211002\Symfony\Component\Console\Helper;

use ECSPrefix20211002\Symfony\Component\Console\Input\InputAwareInterface;
use ECSPrefix20211002\Symfony\Component\Console\Input\InputInterface;
/**
 * An implementation of InputAwareInterface for Helpers.
 *
 * @author Wouter J <waldio.webdesign@gmail.com>
 */
abstract class InputAwareHelper extends \ECSPrefix20211002\Symfony\Component\Console\Helper\Helper implements \ECSPrefix20211002\Symfony\Component\Console\Input\InputAwareInterface
{
    protected $input;
    /**
     * {@inheritdoc}
     * @param \Symfony\Component\Console\Input\InputInterface $input
     */
    public function setInput($input)
    {
        $this->input = $input;
    }
}
