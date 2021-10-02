<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\Storage\Handler;

/**
 * Adds basic `SessionUpdateTimestampHandlerInterface` behaviors to another `SessionHandlerInterface`.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class StrictSessionHandler extends \ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\Storage\Handler\AbstractSessionHandler
{
    private $handler;
    private $doDestroy;
    public function __construct(\SessionHandlerInterface $handler)
    {
        if ($handler instanceof \SessionUpdateTimestampHandlerInterface) {
            throw new \LogicException(\sprintf('"%s" is already an instance of "SessionUpdateTimestampHandlerInterface", you cannot wrap it with "%s".', \get_debug_type($handler), self::class));
        }
        $this->handler = $handler;
    }
    /**
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function open($savePath, $sessionName)
    {
        parent::open($savePath, $sessionName);
        return $this->handler->open($savePath, $sessionName);
    }
    /**
     * {@inheritdoc}
     * @param string $sessionId
     */
    protected function doRead($sessionId)
    {
        return $this->handler->read($sessionId);
    }
    /**
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function updateTimestamp($sessionId, $data)
    {
        return $this->write($sessionId, $data);
    }
    /**
     * {@inheritdoc}
     * @param string $sessionId
     * @param string $data
     */
    protected function doWrite($sessionId, $data)
    {
        return $this->handler->write($sessionId, $data);
    }
    /**
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function destroy($sessionId)
    {
        $this->doDestroy = \true;
        $destroyed = parent::destroy($sessionId);
        return $this->doDestroy ? $this->doDestroy($sessionId) : $destroyed;
    }
    /**
     * {@inheritdoc}
     * @param string $sessionId
     */
    protected function doDestroy($sessionId)
    {
        $this->doDestroy = \false;
        return $this->handler->destroy($sessionId);
    }
    /**
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function close()
    {
        return $this->handler->close();
    }
    /**
     * @return int|false
     */
    #[\ReturnTypeWillChange]
    public function gc($maxlifetime)
    {
        return $this->handler->gc($maxlifetime);
    }
}
