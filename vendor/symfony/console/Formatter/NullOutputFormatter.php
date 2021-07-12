<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210712\Symfony\Component\Console\Formatter;

/**
 * @author Tien Xuan Vo <tien.xuan.vo@gmail.com>
 */
final class NullOutputFormatter implements \ECSPrefix20210712\Symfony\Component\Console\Formatter\OutputFormatterInterface
{
    private $style;
    /**
     * {@inheritdoc}
     * @param string|null $message
     * @return void
     */
    public function format($message)
    {
        // do nothing
    }
    /**
     * {@inheritdoc}
     * @param string $name
     */
    public function getStyle($name) : \ECSPrefix20210712\Symfony\Component\Console\Formatter\OutputFormatterStyleInterface
    {
        if ($this->style) {
            return $this->style;
        }
        // to comply with the interface we must return a OutputFormatterStyleInterface
        return $this->style = new \ECSPrefix20210712\Symfony\Component\Console\Formatter\NullOutputFormatterStyle();
    }
    /**
     * {@inheritdoc}
     * @param string $name
     */
    public function hasStyle($name) : bool
    {
        return \false;
    }
    /**
     * {@inheritdoc}
     */
    public function isDecorated() : bool
    {
        return \false;
    }
    /**
     * {@inheritdoc}
     * @param bool $decorated
     * @return void
     */
    public function setDecorated($decorated)
    {
        // do nothing
    }
    /**
     * {@inheritdoc}
     * @param string $name
     * @param \Symfony\Component\Console\Formatter\OutputFormatterStyleInterface $style
     * @return void
     */
    public function setStyle($name, $style)
    {
        // do nothing
    }
}
