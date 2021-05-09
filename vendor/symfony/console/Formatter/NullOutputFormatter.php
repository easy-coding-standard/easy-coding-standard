<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210509\Symfony\Component\Console\Formatter;

/**
 * @author Tien Xuan Vo <tien.xuan.vo@gmail.com>
 */
final class NullOutputFormatter implements \ECSPrefix20210509\Symfony\Component\Console\Formatter\OutputFormatterInterface
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
     * @return \Symfony\Component\Console\Formatter\OutputFormatterStyleInterface
     */
    public function getStyle($name)
    {
        $name = (string) $name;
        if ($this->style) {
            return $this->style;
        }
        // to comply with the interface we must return a OutputFormatterStyleInterface
        return $this->style = new \ECSPrefix20210509\Symfony\Component\Console\Formatter\NullOutputFormatterStyle();
    }
    /**
     * {@inheritdoc}
     * @param string $name
     * @return bool
     */
    public function hasStyle($name)
    {
        $name = (string) $name;
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
        $decorated = (bool) $decorated;
        // do nothing
    }
    /**
     * {@inheritdoc}
     * @return void
     * @param string $name
     */
    public function setStyle($name, \ECSPrefix20210509\Symfony\Component\Console\Formatter\OutputFormatterStyleInterface $style)
    {
        $name = (string) $name;
        // do nothing
    }
}
