<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210512\Symfony\Component\Console\Formatter;

use ECSPrefix20210512\Symfony\Component\Console\Color;
/**
 * Formatter style class for defining styles.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class OutputFormatterStyle implements \ECSPrefix20210512\Symfony\Component\Console\Formatter\OutputFormatterStyleInterface
{
    private $color;
    private $foreground;
    private $background;
    private $options;
    private $href;
    private $handlesHrefGracefully;
    /**
     * Initializes output formatter style.
     *
     * @param string $foreground The style foreground color name
     * @param string $background The style background color name
     */
    public function __construct($foreground = null, $background = null, array $options = [])
    {
        $this->color = new \ECSPrefix20210512\Symfony\Component\Console\Color($this->foreground = $foreground ?: '', $this->background = $background ?: '', $this->options = $options);
    }
    /**
     * {@inheritdoc}
     * @param string $color
     */
    public function setForeground($color = null)
    {
        $this->color = new \ECSPrefix20210512\Symfony\Component\Console\Color($this->foreground = $color ?: '', $this->background, $this->options);
    }
    /**
     * {@inheritdoc}
     * @param string $color
     */
    public function setBackground($color = null)
    {
        $this->color = new \ECSPrefix20210512\Symfony\Component\Console\Color($this->foreground, $this->background = $color ?: '', $this->options);
    }
    /**
     * @return void
     * @param string $url
     */
    public function setHref($url)
    {
        $url = (string) $url;
        $this->href = $url;
    }
    /**
     * {@inheritdoc}
     * @param string $option
     */
    public function setOption($option)
    {
        $option = (string) $option;
        $this->options[] = $option;
        $this->color = new \ECSPrefix20210512\Symfony\Component\Console\Color($this->foreground, $this->background, $this->options);
    }
    /**
     * {@inheritdoc}
     * @param string $option
     */
    public function unsetOption($option)
    {
        $option = (string) $option;
        $pos = \array_search($option, $this->options);
        if (\false !== $pos) {
            unset($this->options[$pos]);
        }
        $this->color = new \ECSPrefix20210512\Symfony\Component\Console\Color($this->foreground, $this->background, $this->options);
    }
    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options)
    {
        $this->color = new \ECSPrefix20210512\Symfony\Component\Console\Color($this->foreground, $this->background, $this->options = $options);
    }
    /**
     * {@inheritdoc}
     * @param string $text
     */
    public function apply($text)
    {
        $text = (string) $text;
        if (null === $this->handlesHrefGracefully) {
            $this->handlesHrefGracefully = 'JetBrains-JediTerm' !== \getenv('TERMINAL_EMULATOR') && (!\getenv('KONSOLE_VERSION') || (int) \getenv('KONSOLE_VERSION') > 201100);
        }
        if (null !== $this->href && $this->handlesHrefGracefully) {
            $text = "\33]8;;{$this->href}\33\\{$text}\33]8;;\33\\";
        }
        return $this->color->apply($text);
    }
}
