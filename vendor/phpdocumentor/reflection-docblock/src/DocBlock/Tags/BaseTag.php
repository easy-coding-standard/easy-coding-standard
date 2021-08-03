<?php

declare (strict_types=1);
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */
namespace ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags;

use ECSPrefix20210803\phpDocumentor\Reflection\DocBlock;
use ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Description;
/**
 * Parses a tag definition for a DocBlock.
 */
abstract class BaseTag implements \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tag
{
    /** @var string Name of the tag */
    protected $name = '';
    /** @var Description|null Description of the tag. */
    protected $description;
    /**
     * Gets the name of this tag.
     *
     * @return string The name of this tag.
     */
    public function getName() : string
    {
        return $this->name;
    }
    public function getDescription() : ?\ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Description
    {
        return $this->description;
    }
    public function render(?\ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Formatter $formatter = null) : string
    {
        if ($formatter === null) {
            $formatter = new \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Formatter\PassthroughFormatter();
        }
        return $formatter->format($this);
    }
}
