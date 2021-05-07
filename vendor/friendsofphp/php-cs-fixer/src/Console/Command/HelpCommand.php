<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace PhpCsFixer\Console\Command;

use PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use PhpCsFixer\FixerConfiguration\FixerOptionInterface;
use PhpCsFixer\Preg;
use ECSPrefix20210507\Symfony\Component\Console\Command\HelpCommand as BaseHelpCommand;
use ECSPrefix20210507\Symfony\Component\Console\Formatter\OutputFormatterStyle;
use ECSPrefix20210507\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20210507\Symfony\Component\Console\Output\OutputInterface;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 *
 * @internal
 */
final class HelpCommand extends \ECSPrefix20210507\Symfony\Component\Console\Command\HelpCommand
{
    protected static $defaultName = 'help';
    /**
     * @param mixed $value
     * @return string
     */
    public static function toString($value)
    {
        return \is_array($value) ? static::arrayToString($value) : static::scalarToString($value);
    }
    /**
     * Returns the allowed values of the given option that can be converted to a string.
     * @return mixed[]|null
     * @param \PhpCsFixer\FixerConfiguration\FixerOptionInterface $option
     */
    public static function getDisplayableAllowedValues($option)
    {
        $allowed = $option->getAllowedValues();
        if (null !== $allowed) {
            $allowed = \array_filter($allowed, static function ($value) {
                return !$value instanceof \Closure;
            });
            \usort($allowed, static function ($valueA, $valueB) {
                if ($valueA instanceof \PhpCsFixer\FixerConfiguration\AllowedValueSubset) {
                    return -1;
                }
                if ($valueB instanceof \PhpCsFixer\FixerConfiguration\AllowedValueSubset) {
                    return 1;
                }
                return \strcasecmp(self::toString($valueA), self::toString($valueB));
            });
            if (0 === \count($allowed)) {
                $allowed = null;
            }
        }
        return $allowed;
    }
    /**
     * {@inheritdoc}
     * @return void
     * @param \ECSPrefix20210507\Symfony\Component\Console\Input\InputInterface $input
     * @param \ECSPrefix20210507\Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function initialize($input, $output)
    {
        $output->getFormatter()->setStyle('url', new \ECSPrefix20210507\Symfony\Component\Console\Formatter\OutputFormatterStyle('blue'));
    }
    /**
     * Wraps a string to the given number of characters, ignoring style tags.
     *
     * @return mixed[]
     * @param string $string
     * @param int $width
     */
    private static function wordwrap($string, $width)
    {
        $result = [];
        $currentLine = 0;
        $lineLength = 0;
        foreach (\explode(' ', $string) as $word) {
            $wordLength = \strlen(\PhpCsFixer\Preg::replace('~</?(\\w+)>~', '', $word));
            if (0 !== $lineLength) {
                ++$wordLength;
                // space before word
            }
            if ($lineLength + $wordLength > $width) {
                ++$currentLine;
                $lineLength = 0;
            }
            $result[$currentLine][] = $word;
            $lineLength += $wordLength;
        }
        return \array_map(static function (array $line) {
            return \implode(' ', $line);
        }, $result);
    }
    /**
     * @param mixed $value
     * @return string
     */
    private static function scalarToString($value)
    {
        $str = \var_export($value, \true);
        return \PhpCsFixer\Preg::replace('/\\bNULL\\b/', 'null', $str);
    }
    /**
     * @return string
     */
    private static function arrayToString(array $value)
    {
        if (0 === \count($value)) {
            return '[]';
        }
        $isHash = static::isHash($value);
        $str = '[';
        foreach ($value as $k => $v) {
            if ($isHash) {
                $str .= static::scalarToString($k) . ' => ';
            }
            $str .= \is_array($v) ? static::arrayToString($v) . ', ' : static::scalarToString($v) . ', ';
        }
        return \substr($str, 0, -2) . ']';
    }
    /**
     * @return bool
     */
    private static function isHash(array $array)
    {
        $i = 0;
        foreach ($array as $k => $v) {
            if ($k !== $i) {
                return \true;
            }
            ++$i;
        }
        return \false;
    }
}
