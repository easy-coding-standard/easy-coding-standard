<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210511\Symfony\Component\Console;

use ECSPrefix20210511\Symfony\Component\Console\Exception\InvalidArgumentException;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class Color
{
    const COLORS = ['black' => 0, 'red' => 1, 'green' => 2, 'yellow' => 3, 'blue' => 4, 'magenta' => 5, 'cyan' => 6, 'white' => 7, 'default' => 9];
    const AVAILABLE_OPTIONS = ['bold' => ['set' => 1, 'unset' => 22], 'underscore' => ['set' => 4, 'unset' => 24], 'blink' => ['set' => 5, 'unset' => 25], 'reverse' => ['set' => 7, 'unset' => 27], 'conceal' => ['set' => 8, 'unset' => 28]];
    private $foreground;
    private $background;
    private $options = [];
    /**
     * @param string $foreground
     * @param string $background
     */
    public function __construct($foreground = '', $background = '', array $options = [])
    {
        $foreground = (string) $foreground;
        $background = (string) $background;
        $this->foreground = $this->parseColor($foreground);
        $this->background = $this->parseColor($background);
        foreach ($options as $option) {
            if (!isset(self::AVAILABLE_OPTIONS[$option])) {
                throw new \ECSPrefix20210511\Symfony\Component\Console\Exception\InvalidArgumentException(\sprintf('Invalid option specified: "%s". Expected one of (%s).', $option, \implode(', ', \array_keys(self::AVAILABLE_OPTIONS))));
            }
            $this->options[$option] = self::AVAILABLE_OPTIONS[$option];
        }
    }
    /**
     * @param string $text
     * @return string
     */
    public function apply($text)
    {
        $text = (string) $text;
        return $this->set() . $text . $this->unset();
    }
    /**
     * @return string
     */
    public function set()
    {
        $setCodes = [];
        if ('' !== $this->foreground) {
            $setCodes[] = '3' . $this->foreground;
        }
        if ('' !== $this->background) {
            $setCodes[] = '4' . $this->background;
        }
        foreach ($this->options as $option) {
            $setCodes[] = $option['set'];
        }
        if (0 === \count($setCodes)) {
            return '';
        }
        return \sprintf("\33[%sm", \implode(';', $setCodes));
    }
    /**
     * @return string
     */
    public function unset()
    {
        $unsetCodes = [];
        if ('' !== $this->foreground) {
            $unsetCodes[] = 39;
        }
        if ('' !== $this->background) {
            $unsetCodes[] = 49;
        }
        foreach ($this->options as $option) {
            $unsetCodes[] = $option['unset'];
        }
        if (0 === \count($unsetCodes)) {
            return '';
        }
        return \sprintf("\33[%sm", \implode(';', $unsetCodes));
    }
    /**
     * @param string $color
     * @return string
     */
    private function parseColor($color)
    {
        $color = (string) $color;
        if ('' === $color) {
            return '';
        }
        if ('#' === $color[0]) {
            $color = \substr($color, 1);
            if (3 === \strlen($color)) {
                $color = $color[0] . $color[0] . $color[1] . $color[1] . $color[2] . $color[2];
            }
            if (6 !== \strlen($color)) {
                throw new \ECSPrefix20210511\Symfony\Component\Console\Exception\InvalidArgumentException(\sprintf('Invalid "%s" color.', $color));
            }
            return $this->convertHexColorToAnsi(\hexdec($color));
        }
        if (!isset(self::COLORS[$color])) {
            throw new \ECSPrefix20210511\Symfony\Component\Console\Exception\InvalidArgumentException(\sprintf('Invalid "%s" color; expected one of (%s).', $color, \implode(', ', \array_keys(self::COLORS))));
        }
        return (string) self::COLORS[$color];
    }
    /**
     * @param int $color
     * @return string
     */
    private function convertHexColorToAnsi($color)
    {
        $color = (int) $color;
        $r = $color >> 16 & 255;
        $g = $color >> 8 & 255;
        $b = $color & 255;
        // see https://github.com/termstandard/colors/ for more information about true color support
        if ('truecolor' !== \getenv('COLORTERM')) {
            return (string) $this->degradeHexColorToAnsi($r, $g, $b);
        }
        return \sprintf('8;2;%d;%d;%d', $r, $g, $b);
    }
    /**
     * @param int $r
     * @param int $g
     * @param int $b
     * @return int
     */
    private function degradeHexColorToAnsi($r, $g, $b)
    {
        $r = (int) $r;
        $g = (int) $g;
        $b = (int) $b;
        if (0 === \round($this->getSaturation($r, $g, $b) / 50)) {
            return 0;
        }
        return \round($b / 255) << 2 | \round($g / 255) << 1 | \round($r / 255);
    }
    /**
     * @param int $r
     * @param int $g
     * @param int $b
     * @return int
     */
    private function getSaturation($r, $g, $b)
    {
        $r = (int) $r;
        $g = (int) $g;
        $b = (int) $b;
        $r = $r / 255;
        $g = $g / 255;
        $b = $b / 255;
        $v = \max($r, $g, $b);
        if (0 === ($diff = $v - \min($r, $g, $b))) {
            return 0;
        }
        return (int) $diff * 100 / $v;
    }
}
