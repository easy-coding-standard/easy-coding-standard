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
namespace PhpCsFixer\Doctrine\Annotation;

use ECSPrefix20210513\Doctrine\Common\Annotations\DocLexer;
/**
 * A Doctrine annotation token.
 *
 * @internal
 */
final class Token
{
    /**
     * @var int
     */
    private $type;
    /**
     * @var string
     */
    private $content;
    /**
     * @param int    $type    The type
     * @param string $content The content
     */
    public function __construct($type = \ECSPrefix20210513\Doctrine\Common\Annotations\DocLexer::T_NONE, $content = '')
    {
        $type = (int) $type;
        $content = (string) $content;
        $this->type = $type;
        $this->content = $content;
    }
    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * @return void
     * @param int $type
     */
    public function setType($type)
    {
        $type = (int) $type;
        $this->type = $type;
    }
    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
    /**
     * @return void
     * @param string $content
     */
    public function setContent($content)
    {
        $content = (string) $content;
        $this->content = $content;
    }
    /**
     * Returns whether the token type is one of the given types.
     *
     * @param int|int[] $types
     * @return bool
     */
    public function isType($types)
    {
        if (!\is_array($types)) {
            $types = [$types];
        }
        return \in_array($this->getType(), $types, \true);
    }
    /**
     * Overrides the content with an empty string.
     * @return void
     */
    public function clear()
    {
        $this->setContent('');
    }
}
