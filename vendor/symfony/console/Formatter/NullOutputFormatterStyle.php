<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Component\Console\Formatter;

/**
 * @author Tien Xuan Vo <tien.xuan.vo@gmail.com>
 */
final class NullOutputFormatterStyle implements \ECSPrefix20210507\Symfony\Component\Console\Formatter\OutputFormatterStyleInterface
{
    /**
     * {@inheritdoc}
     * @param string $text
     * @return string
     */
    public function apply($text)
    {
        return $text;
    }
    /**
     * {@inheritdoc}
     * @return void
     * @param string $color
     */
    public function setBackground($color = null)
    {
        // do nothing
    }
    /**
     * {@inheritdoc}
     * @return void
     * @param string $color
     */
    public function setForeground($color = null)
    {
        // do nothing
    }
    /**
     * {@inheritdoc}
     * @return void
     * @param string $option
     */
    public function setOption($option)
    {
        // do nothing
    }
    /**
     * {@inheritdoc}
     * @return void
     */
    public function setOptions(array $options)
    {
        // do nothing
    }
    /**
     * {@inheritdoc}
     * @return void
     * @param string $option
     */
    public function unsetOption($option)
    {
        // do nothing
    }
}
