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
final class NullOutputFormatterStyle implements \ECSPrefix20210508\Symfony\Component\Console\Formatter\OutputFormatterStyleInterface
{
    /**
     * {@inheritdoc}
     * @param string $text
     */
    public function apply($text) : string
    {
        if (\is_object($text)) {
            $text = (string) $text;
        }
        return $text;
    }
    /**
     * {@inheritdoc}
     * @return void
     * @param string $color
     */
    public function setBackground($color = null)
    {
        if (\is_object($color)) {
            $color = (string) $color;
        }
        // do nothing
    }
    /**
     * {@inheritdoc}
     * @return void
     * @param string $color
     */
    public function setForeground($color = null)
    {
        if (\is_object($color)) {
            $color = (string) $color;
        }
        // do nothing
    }
    /**
     * {@inheritdoc}
     * @return void
     * @param string $option
     */
    public function setOption($option)
    {
        if (\is_object($option)) {
            $option = (string) $option;
        }
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
        if (\is_object($option)) {
            $option = (string) $option;
        }
        // do nothing
    }
}
