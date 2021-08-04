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
namespace ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\Tags;

use ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\Description;
use ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\DescriptionFactory;
use ECSPrefix20210804\phpDocumentor\Reflection\Types\Context as TypeContext;
use ECSPrefix20210804\phpDocumentor\Reflection\Utils;
use ECSPrefix20210804\Webmozart\Assert\Assert;
/**
 * Reflection class for a {@}link tag in a Docblock.
 */
final class Link extends \ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\Tags\BaseTag implements \ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\Tags\Factory\StaticMethod
{
    /** @var string */
    protected $name = 'link';
    /** @var string */
    private $link;
    /**
     * Initializes a link to a URL.
     */
    public function __construct(string $link, ?\ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\Description $description = null)
    {
        $this->link = $link;
        $this->description = $description;
    }
    public static function create(string $body, ?\ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\DescriptionFactory $descriptionFactory = null, ?\ECSPrefix20210804\phpDocumentor\Reflection\Types\Context $context = null) : self
    {
        \ECSPrefix20210804\Webmozart\Assert\Assert::notNull($descriptionFactory);
        $parts = \ECSPrefix20210804\phpDocumentor\Reflection\Utils::pregSplit('/\\s+/Su', $body, 2);
        $description = isset($parts[1]) ? $descriptionFactory->create($parts[1], $context) : null;
        return new static($parts[0], $description);
    }
    /**
     * Gets the link
     */
    public function getLink() : string
    {
        return $this->link;
    }
    /**
     * Returns a string representation for this tag.
     */
    public function __toString() : string
    {
        if ($this->description) {
            $description = $this->description->render();
        } else {
            $description = '';
        }
        $link = (string) $this->link;
        return $link . ($description !== '' ? ($link !== '' ? ' ' : '') . $description : '');
    }
}
