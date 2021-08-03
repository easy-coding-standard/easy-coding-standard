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
use ECSPrefix20210803\Webmozart\Assert\Assert;
/**
 * Reflection class for a {@}return tag in a Docblock.
 */
final class Return_ extends \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\TagWithType implements \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Factory\StaticMethod
{
    public function __construct(\ECSPrefix20210803\phpDocumentor\Reflection\Type $type, ?\ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Description $description = null)
    {
        $this->name = 'return';
        $this->type = $type;
        $this->description = $description;
    }
    public static function create(string $body, ?\ECSPrefix20210803\phpDocumentor\Reflection\TypeResolver $typeResolver = null, ?\ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\DescriptionFactory $descriptionFactory = null, ?\ECSPrefix20210803\phpDocumentor\Reflection\Types\Context $context = null) : self
    {
        \ECSPrefix20210803\Webmozart\Assert\Assert::notNull($typeResolver);
        \ECSPrefix20210803\Webmozart\Assert\Assert::notNull($descriptionFactory);
        [$type, $description] = self::extractTypeFromBody($body);
        $type = $typeResolver->resolve($type, $context);
        $description = $descriptionFactory->create($description, $context);
        return new static($type, $description);
    }
    public function __toString() : string
    {
        if ($this->description) {
            $description = $this->description->render();
        } else {
            $description = '';
        }
        $type = $this->type ? '' . $this->type : 'mixed';
        return $type . ($description !== '' ? ($type !== '' ? ' ' : '') . $description : '');
    }
}
