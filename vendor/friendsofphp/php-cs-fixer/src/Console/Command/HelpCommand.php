<?php

declare (strict_types=1);
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
use ECSPrefix20220220\Symfony\Component\Console\Command\HelpCommand as BaseHelpCommand;
use ECSPrefix20220220\Symfony\Component\Console\Formatter\OutputFormatterStyle;
use ECSPrefix20220220\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20220220\Symfony\Component\Console\Output\OutputInterface;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class HelpCommand extends \ECSPrefix20220220\Symfony\Component\Console\Command\HelpCommand
{
    /**
     * @var string
     */
    protected static $defaultName = 'help';
    /**
     * @param mixed $value
     */
    public static function toString($value) : string
    {
        return \is_array($value) ? static::arrayToString($value) : static::scalarToString($value);
    }
    /**
     * Returns the allowed values of the given option that can be converted to a string.
     */
    public static function getDisplayableAllowedValues(\PhpCsFixer\FixerConfiguration\FixerOptionInterface $option) : ?array
    {
        $allowed = $option->getAllowedValues();
        if (null !== $allowed) {
            $allowed = \array_filter($allowed, static function ($value) : bool {
                return !$value instanceof \Closure;
            });
            \usort($allowed, static function ($valueA, $valueB) : int {
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
     */
    protected function initialize(\ECSPrefix20220220\Symfony\Component\Console\Input\InputInterface $input, \ECSPrefix20220220\Symfony\Component\Console\Output\OutputInterface $output) : void
    {
        $output->getFormatter()->setStyle('url', new \ECSPrefix20220220\Symfony\Component\Console\Formatter\OutputFormatterStyle('blue'));
    }
    /**
     * @param mixed $value
     */
    private static function scalarToString($value) : string
    {
        $str = \var_export($value, \true);
        return \PhpCsFixer\Preg::replace('/\\bNULL\\b/', 'null', $str);
    }
    private static function arrayToString(array $value) : string
    {
        if (0 === \count($value)) {
            return '[]';
        }
        $isHash = !array_is_list($value);
        $str = '[';
        foreach ($value as $k => $v) {
            if ($isHash) {
                $str .= static::scalarToString($k) . ' => ';
            }
            $str .= \is_array($v) ? static::arrayToString($v) . ', ' : static::scalarToString($v) . ', ';
        }
        return \substr($str, 0, -2) . ']';
    }
}
