<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\Symfony\Component\Console\Formatter;

/**
 * @author Tien Xuan Vo <tien.xuan.vo@gmail.com>
 */
final class NullOutputFormatter implements \ECSPrefix20210508\Symfony\Component\Console\Formatter\OutputFormatterInterface
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
    public function getStyle($name) : \ECSPrefix20210508\Symfony\Component\Console\Formatter\OutputFormatterStyleInterface
    {
        if (\is_object($name)) {
            $name = (string) $name;
        }
        if ($this->style) {
            return $this->style;
        }
        // to comply with the interface we must return a OutputFormatterStyleInterface
        return $this->style = new \ECSPrefix20210508\Symfony\Component\Console\Formatter\NullOutputFormatterStyle();
    }
    /**
     * {@inheritdoc}
     * @param string $name
     */
    public function hasStyle($name) : bool
    {
        if (\is_object($name)) {
            $name = (string) $name;
        }
        return \false;
    }
    /**
     * {@inheritdoc}
     * @return bool
     */
    public function isDecorated()
    {
        return \false;
    }
    /**
     * {@inheritdoc}
     * @return void
     * @param bool $decorated
     */
    public function setDecorated($decorated)
    {
        // do nothing
    }
    /**
     * {@inheritdoc}
     * @return void
     * @param string $name
     */
    public function setStyle($name, \ECSPrefix20210508\Symfony\Component\Console\Formatter\OutputFormatterStyleInterface $style)
    {
        if (\is_object($name)) {
            $name = (string) $name;
        }
        // do nothing
    }
}
