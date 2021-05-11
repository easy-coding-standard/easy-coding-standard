<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210511\Symfony\Component\Console\Formatter;

use ECSPrefix20210511\Symfony\Component\Console\Exception\InvalidArgumentException;
/**
 * Formatter class for console output.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
class OutputFormatter implements \ECSPrefix20210511\Symfony\Component\Console\Formatter\WrappableOutputFormatterInterface
{
    private $decorated;
    private $styles = [];
    private $styleStack;
    public function __clone()
    {
        $this->styleStack = clone $this->styleStack;
        foreach ($this->styles as $key => $value) {
            $this->styles[$key] = clone $value;
        }
    }
    /**
     * Escapes "<" special char in given text.
     *
     * @return string Escaped text
     * @param string $text
     */
    public static function escape($text)
    {
        $text = (string) $text;
        $text = \preg_replace('/([^\\\\]?)</', '$1\\<', $text);
        return self::escapeTrailingBackslash($text);
    }
    /**
     * Escapes trailing "\" in given text.
     *
     * @internal
     * @param string $text
     * @return string
     */
    public static function escapeTrailingBackslash($text)
    {
        $text = (string) $text;
        if ('\\' === \substr($text, -1)) {
            $len = \strlen($text);
            $text = \rtrim($text, '\\');
            $text = \str_replace("\0", '', $text);
            $text .= \str_repeat("\0", $len - \strlen($text));
        }
        return $text;
    }
    /**
     * Initializes console output formatter.
     *
     * @param OutputFormatterStyleInterface[] $styles Array of "name => FormatterStyle" instances
     * @param bool $decorated
     */
    public function __construct($decorated = \false, array $styles = [])
    {
        $decorated = (bool) $decorated;
        $this->decorated = $decorated;
        $this->setStyle('error', new \ECSPrefix20210511\Symfony\Component\Console\Formatter\OutputFormatterStyle('white', 'red'));
        $this->setStyle('info', new \ECSPrefix20210511\Symfony\Component\Console\Formatter\OutputFormatterStyle('green'));
        $this->setStyle('comment', new \ECSPrefix20210511\Symfony\Component\Console\Formatter\OutputFormatterStyle('yellow'));
        $this->setStyle('question', new \ECSPrefix20210511\Symfony\Component\Console\Formatter\OutputFormatterStyle('black', 'cyan'));
        foreach ($styles as $name => $style) {
            $this->setStyle($name, $style);
        }
        $this->styleStack = new \ECSPrefix20210511\Symfony\Component\Console\Formatter\OutputFormatterStyleStack();
    }
    /**
     * {@inheritdoc}
     * @param bool $decorated
     */
    public function setDecorated($decorated)
    {
        $decorated = (bool) $decorated;
        $this->decorated = $decorated;
    }
    /**
     * {@inheritdoc}
     */
    public function isDecorated()
    {
        return $this->decorated;
    }
    /**
     * {@inheritdoc}
     * @param string $name
     */
    public function setStyle($name, \ECSPrefix20210511\Symfony\Component\Console\Formatter\OutputFormatterStyleInterface $style)
    {
        $name = (string) $name;
        $this->styles[\strtolower($name)] = $style;
    }
    /**
     * {@inheritdoc}
     * @param string $name
     */
    public function hasStyle($name)
    {
        $name = (string) $name;
        return isset($this->styles[\strtolower($name)]);
    }
    /**
     * {@inheritdoc}
     * @param string $name
     */
    public function getStyle($name)
    {
        $name = (string) $name;
        if (!$this->hasStyle($name)) {
            throw new \ECSPrefix20210511\Symfony\Component\Console\Exception\InvalidArgumentException(\sprintf('Undefined style: "%s".', $name));
        }
        return $this->styles[\strtolower($name)];
    }
    /**
     * {@inheritdoc}
     * @param string|null $message
     */
    public function format($message)
    {
        return $this->formatAndWrap($message, 0);
    }
    /**
     * {@inheritdoc}
     * @param string|null $message
     * @param int $width
     */
    public function formatAndWrap($message, $width)
    {
        $width = (int) $width;
        $offset = 0;
        $output = '';
        $tagRegex = '[a-z][^<>]*+';
        $currentLineLength = 0;
        \preg_match_all("#<(({$tagRegex}) | /({$tagRegex})?)>#ix", $message, $matches, \PREG_OFFSET_CAPTURE);
        foreach ($matches[0] as $i => $match) {
            $pos = $match[1];
            $text = $match[0];
            if (0 != $pos && '\\' == $message[$pos - 1]) {
                continue;
            }
            // add the text up to the next tag
            $output .= $this->applyCurrentStyle(\substr($message, $offset, $pos - $offset), $output, $width, $currentLineLength);
            $offset = $pos + \strlen($text);
            // opening tag?
            if ($open = '/' != $text[1]) {
                $tag = $matches[1][$i][0];
            } else {
                $tag = isset($matches[3][$i][0]) ? $matches[3][$i][0] : '';
            }
            if (!$open && !$tag) {
                // </>
                $this->styleStack->pop();
            } elseif (null === ($style = $this->createStyleFromString($tag))) {
                $output .= $this->applyCurrentStyle($text, $output, $width, $currentLineLength);
            } elseif ($open) {
                $this->styleStack->push($style);
            } else {
                $this->styleStack->pop($style);
            }
        }
        $output .= $this->applyCurrentStyle(\substr($message, $offset), $output, $width, $currentLineLength);
        if (\false !== \strpos($output, "\0")) {
            return \strtr($output, ["\0" => '\\', '\\<' => '<']);
        }
        return \str_replace('\\<', '<', $output);
    }
    /**
     * @return OutputFormatterStyleStack
     */
    public function getStyleStack()
    {
        return $this->styleStack;
    }
    /**
     * Tries to create new style instance from string.
     * @return \Symfony\Component\Console\Formatter\OutputFormatterStyleInterface|null
     * @param string $string
     */
    private function createStyleFromString($string)
    {
        $string = (string) $string;
        if (isset($this->styles[$string])) {
            return $this->styles[$string];
        }
        if (!\preg_match_all('/([^=]+)=([^;]+)(;|$)/', $string, $matches, \PREG_SET_ORDER)) {
            return null;
        }
        $style = new \ECSPrefix20210511\Symfony\Component\Console\Formatter\OutputFormatterStyle();
        foreach ($matches as $match) {
            \array_shift($match);
            $match[0] = \strtolower($match[0]);
            if ('fg' == $match[0]) {
                $style->setForeground(\strtolower($match[1]));
            } elseif ('bg' == $match[0]) {
                $style->setBackground(\strtolower($match[1]));
            } elseif ('href' === $match[0]) {
                $style->setHref($match[1]);
            } elseif ('options' === $match[0]) {
                \preg_match_all('([^,;]+)', \strtolower($match[1]), $options);
                $options = \array_shift($options);
                foreach ($options as $option) {
                    $style->setOption($option);
                }
            } else {
                return null;
            }
        }
        return $style;
    }
    /**
     * Applies current style from stack to text, if must be applied.
     * @param string $text
     * @param string $current
     * @param int $width
     * @param int $currentLineLength
     * @return string
     */
    private function applyCurrentStyle($text, $current, $width, &$currentLineLength)
    {
        $text = (string) $text;
        $current = (string) $current;
        $width = (int) $width;
        $currentLineLength = (int) $currentLineLength;
        if ('' === $text) {
            return '';
        }
        if (!$width) {
            return $this->isDecorated() ? $this->styleStack->getCurrent()->apply($text) : $text;
        }
        if (!$currentLineLength && '' !== $current) {
            $text = \ltrim($text);
        }
        if ($currentLineLength) {
            $prefix = \substr($text, 0, $i = $width - $currentLineLength) . "\n";
            $text = \substr($text, $i);
        } else {
            $prefix = '';
        }
        \preg_match('~(\\n)$~', $text, $matches);
        $text = $prefix . \preg_replace('~([^\\n]{' . $width . '})\\ *~', "\$1\n", $text);
        $text = \rtrim($text, "\n") . (isset($matches[1]) ? $matches[1] : '');
        if (!$currentLineLength && '' !== $current && "\n" !== \substr($current, -1)) {
            $text = "\n" . $text;
        }
        $lines = \explode("\n", $text);
        foreach ($lines as $line) {
            $currentLineLength += \strlen($line);
            if ($width <= $currentLineLength) {
                $currentLineLength = 0;
            }
        }
        if ($this->isDecorated()) {
            foreach ($lines as $i => $line) {
                $lines[$i] = $this->styleStack->getCurrent()->apply($line);
            }
        }
        return \implode("\n", $lines);
    }
}
