<?php

namespace ECSPrefix20220220\React\Stream;

use ECSPrefix20220220\Evenement\EventEmitter;
final class CompositeStream extends \ECSPrefix20220220\Evenement\EventEmitter implements \ECSPrefix20220220\React\Stream\DuplexStreamInterface
{
    private $readable;
    private $writable;
    private $closed = \false;
    public function __construct(\ECSPrefix20220220\React\Stream\ReadableStreamInterface $readable, \ECSPrefix20220220\React\Stream\WritableStreamInterface $writable)
    {
        $this->readable = $readable;
        $this->writable = $writable;
        if (!$readable->isReadable() || !$writable->isWritable()) {
            $this->close();
            return;
        }
        \ECSPrefix20220220\React\Stream\Util::forwardEvents($this->readable, $this, array('data', 'end', 'error'));
        \ECSPrefix20220220\React\Stream\Util::forwardEvents($this->writable, $this, array('drain', 'error', 'pipe'));
        $this->readable->on('close', array($this, 'close'));
        $this->writable->on('close', array($this, 'close'));
    }
    public function isReadable()
    {
        return $this->readable->isReadable();
    }
    public function pause()
    {
        $this->readable->pause();
    }
    public function resume()
    {
        if (!$this->writable->isWritable()) {
            return;
        }
        $this->readable->resume();
    }
    public function pipe(\ECSPrefix20220220\React\Stream\WritableStreamInterface $dest, array $options = array())
    {
        return \ECSPrefix20220220\React\Stream\Util::pipe($this, $dest, $options);
    }
    public function isWritable()
    {
        return $this->writable->isWritable();
    }
    public function write($data)
    {
        return $this->writable->write($data);
    }
    public function end($data = null)
    {
        $this->readable->pause();
        $this->writable->end($data);
    }
    public function close()
    {
        if ($this->closed) {
            return;
        }
        $this->closed = \true;
        $this->readable->close();
        $this->writable->close();
        $this->emit('close');
        $this->removeAllListeners();
    }
}
