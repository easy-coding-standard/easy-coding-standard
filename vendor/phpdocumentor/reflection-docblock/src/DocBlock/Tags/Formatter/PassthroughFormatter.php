<?php

declare (strict_types=1);
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */
namespace ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Formatter;

use ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tag;
use ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Formatter;
use function trim;
class PassthroughFormatter implements \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Formatter
{
    /**
     * Formats the given tag to return a simple plain text version.
     */
    public function format(\ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tag $tag) : string
    {
        return \trim('@' . $tag->getName() . ' ' . $tag);
    }
}
