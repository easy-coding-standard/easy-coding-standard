<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210613\Symfony\Component\EventDispatcher\Attribute;

/**
 * Service tag to autoconfigure event listeners.
 *
 * @author Alexander M. Turek <me@derrabus.de>
 * @Attribute
 */
class AsEventListener
{
    /**
     * @var string|null
     */
    public $event;
    /**
     * @var string|null
     */
    public $method;
    /**
     * @var int
     */
    public $priority = 0;
    /**
     * @var string|null
     */
    public $dispatcher;
    /**
     * @param string|null $event
     * @param string|null $method
     * @param string|null $dispatcher
     */
    public function __construct($event = null, $method = null, int $priority = 0, $dispatcher = null)
    {
        $this->event = $event;
        $this->method = $method;
        $this->priority = $priority;
        $this->dispatcher = $dispatcher;
    }
}
