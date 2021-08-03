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

use ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Description;
use ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\DescriptionFactory;
use ECSPrefix20210803\phpDocumentor\Reflection\Type;
use ECSPrefix20210803\phpDocumentor\Reflection\TypeResolver;
use ECSPrefix20210803\phpDocumentor\Reflection\Types\Context as TypeContext;
use ECSPrefix20210803\phpDocumentor\Reflection\Utils;
use ECSPrefix20210803\Webmozart\Assert\Assert;
use function array_shift;
use function array_unshift;
use function implode;
use function strpos;
use function substr;
use const PREG_SPLIT_DELIM_CAPTURE;
/**
 * Reflection class for a {@}property-write tag in a Docblock.
 */
final class PropertyWrite extends \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\TagWithType implements \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Factory\StaticMethod
{
    /** @var string */
    protected $variableName;
    public function __construct(?string $variableName, ?\ECSPrefix20210803\phpDocumentor\Reflection\Type $type = null, ?\ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Description $description = null)
    {
        \ECSPrefix20210803\Webmozart\Assert\Assert::string($variableName);
        $this->name = 'property-write';
        $this->variableName = $variableName;
        $this->type = $type;
        $this->description = $description;
    }
    public static function create(string $body, ?\ECSPrefix20210803\phpDocumentor\Reflection\TypeResolver $typeResolver = null, ?\ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\DescriptionFactory $descriptionFactory = null, ?\ECSPrefix20210803\phpDocumentor\Reflection\Types\Context $context = null) : self
    {
        \ECSPrefix20210803\Webmozart\Assert\Assert::stringNotEmpty($body);
        \ECSPrefix20210803\Webmozart\Assert\Assert::notNull($typeResolver);
        \ECSPrefix20210803\Webmozart\Assert\Assert::notNull($descriptionFactory);
        [$firstPart, $body] = self::extractTypeFromBody($body);
        $type = null;
        $parts = \ECSPrefix20210803\phpDocumentor\Reflection\Utils::pregSplit('/(\\s+)/Su', $body, 2, \PREG_SPLIT_DELIM_CAPTURE);
        $variableName = '';
        // if the first item that is encountered is not a variable; it is a type
        if ($firstPart && $firstPart[0] !== '$') {
            $type = $typeResolver->resolve($firstPart, $context);
        } else {
            // first part is not a type; we should prepend it to the parts array for further processing
            \array_unshift($parts, $firstPart);
        }
        // if the next item starts with a $ it must be the variable name
        if (isset($parts[0]) && \strpos($parts[0], '$') === 0) {
            $variableName = \array_shift($parts);
            if ($type) {
                \array_shift($parts);
            }
            \ECSPrefix20210803\Webmozart\Assert\Assert::notNull($variableName);
            $variableName = \substr($variableName, 1);
        }
        $description = $descriptionFactory->create(\implode('', $parts), $context);
        return new static($variableName, $type, $description);
    }
    /**
     * Returns the variable's name.
     */
    public function getVariableName() : ?string
    {
        return $this->variableName;
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
        if ($this->variableName) {
            $variableName = '$' . $this->variableName;
        } else {
            $variableName = '';
        }
        $type = (string) $this->type;
        return $type . ($variableName !== '' ? ($type !== '' ? ' ' : '') . $variableName : '') . ($description !== '' ? ($type !== '' || $variableName !== '' ? ' ' : '') . $description : '');
    }
}
