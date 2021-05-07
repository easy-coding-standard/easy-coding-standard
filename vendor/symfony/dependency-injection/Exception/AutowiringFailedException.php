<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Component\DependencyInjection\Exception;

class AnonymousFor_AutowiringFailedException
{
    private $message;
    private $messageCallback;
    public function __construct(&$message, &$messageCallback)
    {
        $this->message =& $message;
        $this->messageCallback =& $messageCallback;
    }
    public function __toString() : string
    {
        $messageCallback = $this->messageCallback;
        $this->messageCallback = null;
        try {
            return $this->message = $messageCallback();
        } catch (\Throwable $e) {
            return $this->message = $e->getMessage();
        }
    }
}
/**
 * Thrown when a definition cannot be autowired.
 */
class AutowiringFailedException extends \ECSPrefix20210507\Symfony\Component\DependencyInjection\Exception\RuntimeException
{
    private $serviceId;
    private $messageCallback;
    /**
     * @param string $serviceId
     * @param int $code
     * @param \Throwable $previous
     */
    public function __construct($serviceId, $message = '', $code = 0, $previous = null)
    {
        $this->serviceId = $serviceId;
        if ($message instanceof \Closure && (\function_exists('xdebug_is_enabled') ? \xdebug_is_enabled() : \function_exists('xdebug_info'))) {
            $message = $message();
        }
        if (!$message instanceof \Closure) {
            parent::__construct($message, $code, $previous);
            return;
        }
        $this->messageCallback = $message;
        parent::__construct('', $code, $previous);
        $this->message = new \ECSPrefix20210507\Symfony\Component\DependencyInjection\Exception\AnonymousFor_AutowiringFailedException($this->message, $this->messageCallback);
    }
    /**
     * @return \Closure|null
     */
    public function getMessageCallback()
    {
        return $this->messageCallback;
    }
    public function getServiceId()
    {
        return $this->serviceId;
    }
}
