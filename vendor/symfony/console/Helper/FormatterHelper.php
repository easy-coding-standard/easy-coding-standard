<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\Symfony\Component\Console\Helper;

use ECSPrefix20210508\Symfony\Component\Console\Formatter\OutputFormatter;
/**
 * The Formatter class provides helpers to format messages.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class FormatterHelper extends \ECSPrefix20210508\Symfony\Component\Console\Helper\Helper
{
    /**
     * Formats a message within a section.
     *
     * @return string The format section
     * @param string $section
     * @param string $message
     * @param string $style
     */
    public function formatSection($section, $message, $style = 'info')
    {
        $section = (string) $section;
        $message = (string) $message;
        $style = (string) $style;
        return \sprintf('<%s>[%s]</%s> %s', $style, $section, $style, $message);
    }
    /**
     * Formats a message as a block of text.
     *
     * @param string|array $messages The message to write in the block
     *
     * @return string The formatter message
     * @param string $style
     * @param bool $large
     */
    public function formatBlock($messages, $style, $large = \false)
    {
        $style = (string) $style;
        $large = (bool) $large;
        if (!\is_array($messages)) {
            $messages = [$messages];
        }
        $len = 0;
        $lines = [];
        foreach ($messages as $message) {
            $message = \ECSPrefix20210508\Symfony\Component\Console\Formatter\OutputFormatter::escape($message);
            $lines[] = \sprintf($large ? '  %s  ' : ' %s ', $message);
            $len = \max(self::strlen($message) + ($large ? 4 : 2), $len);
        }
        $messages = $large ? [\str_repeat(' ', $len)] : [];
        for ($i = 0; isset($lines[$i]); ++$i) {
            $messages[] = $lines[$i] . \str_repeat(' ', $len - self::strlen($lines[$i]));
        }
        if ($large) {
            $messages[] = \str_repeat(' ', $len);
        }
        for ($i = 0; isset($messages[$i]); ++$i) {
            $messages[$i] = \sprintf('<%s>%s</%s>', $style, $messages[$i], $style);
        }
        return \implode("\n", $messages);
    }
    /**
     * Truncates a message to the given length.
     *
     * @return string
     * @param string $message
     * @param int $length
     * @param string $suffix
     */
    public function truncate($message, $length, $suffix = '...')
    {
        $message = (string) $message;
        $length = (int) $length;
        $suffix = (string) $suffix;
        $computedLength = $length - self::strlen($suffix);
        if ($computedLength > self::strlen($message)) {
            return $message;
        }
        return self::substr($message, 0, $length) . $suffix;
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'formatter';
    }
}
