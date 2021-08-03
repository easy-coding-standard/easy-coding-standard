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
namespace ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags;

use InvalidArgumentException;
use ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Description;
use ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\DescriptionFactory;
use ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\StandardTagFactory;
use ECSPrefix20210803\phpDocumentor\Reflection\Types\Context as TypeContext;
use ECSPrefix20210803\Webmozart\Assert\Assert;
use function preg_match;
/**
 * Parses a tag definition for a DocBlock.
 */
final class Generic extends \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\BaseTag implements \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Factory\StaticMethod
{
    /**
     * Parses a tag and populates the member variables.
     *
     * @param string      $name        Name of the tag.
     * @param Description $description The contents of the given tag.
     */
    public function __construct(string $name, ?\ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Description $description = null)
    {
        $this->validateTagName($name);
        $this->name = $name;
        $this->description = $description;
    }
    /**
     * Creates a new tag that represents any unknown tag type.
     *
     * @return static
     */
    public static function create(string $body, string $name = '', ?\ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\DescriptionFactory $descriptionFactory = null, ?\ECSPrefix20210803\phpDocumentor\Reflection\Types\Context $context = null) : self
    {
        \ECSPrefix20210803\Webmozart\Assert\Assert::stringNotEmpty($name);
        \ECSPrefix20210803\Webmozart\Assert\Assert::notNull($descriptionFactory);
        $description = $body !== '' ? $descriptionFactory->create($body, $context) : null;
        return new static($name, $description);
    }
    /**
     * Returns the tag as a serialized string
     */
    public function __toString() : string
    {
        if ($this->description) {
            $description = $this->description->render();
        } else {
            $description = '';
        }
        return $description;
    }
    /**
     * Validates if the tag name matches the expected format, otherwise throws an exception.
     */
    private function validateTagName(string $name) : void
    {
        if (!\preg_match('/^' . \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\StandardTagFactory::REGEX_TAGNAME . '$/u', $name)) {
            throw new \InvalidArgumentException('The tag name "' . $name . '" is not wellformed. Tags may only consist of letters, underscores, ' . 'hyphens and backslashes.');
        }
    }
}
