<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Component\DependencyInjection;

/**
 * Parameter represents a parameter reference.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Parameter
{
    private $id;
    /**
     * @param string $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }
    /**
     * @return string The parameter key
     */
    public function __toString()
    {
        return $this->id;
    }
}
