<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Component\DependencyInjection\Argument;

/**
 * Represents an abstract service argument, which have to be set by a compiler pass or a DI extension.
 */
final class AbstractArgument
{
    private $text;
    private $context;
    /**
     * @param string $text
     */
    public function __construct($text = '')
    {
        $this->text = \trim($text, '. ');
    }
    /**
     * @return void
     * @param string $context
     */
    public function setContext($context)
    {
        $this->context = $context . ' is abstract' . ('' === $this->text ? '' : ': ');
    }
    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }
    /**
     * @return string
     */
    public function getTextWithContext()
    {
        return $this->context . $this->text . '.';
    }
}
