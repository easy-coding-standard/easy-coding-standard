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
use ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\Tags\Reference\Fqsen as FqsenRef;
use ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\Tags\Reference\Reference;
use ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\Tags\Reference\Url;
use ECSPrefix20210804\phpDocumentor\Reflection\Fqsen;
use ECSPrefix20210804\phpDocumentor\Reflection\FqsenResolver;
use ECSPrefix20210804\phpDocumentor\Reflection\Types\Context as TypeContext;
use ECSPrefix20210804\phpDocumentor\Reflection\Utils;
use ECSPrefix20210804\Webmozart\Assert\Assert;
use function array_key_exists;
use function explode;
use function preg_match;
/**
 * Reflection class for an {@}see tag in a Docblock.
 */
final class See extends \ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\Tags\BaseTag implements \ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\Tags\Factory\StaticMethod
{
    /** @var string */
    protected $name = 'see';
    /** @var Reference */
    protected $refers;
    /**
     * Initializes this tag.
     */
    public function __construct(\ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\Tags\Reference\Reference $refers, ?\ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\Description $description = null)
    {
        $this->refers = $refers;
        $this->description = $description;
    }
    public static function create(string $body, ?\ECSPrefix20210804\phpDocumentor\Reflection\FqsenResolver $typeResolver = null, ?\ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\DescriptionFactory $descriptionFactory = null, ?\ECSPrefix20210804\phpDocumentor\Reflection\Types\Context $context = null) : self
    {
        \ECSPrefix20210804\Webmozart\Assert\Assert::notNull($descriptionFactory);
        $parts = \ECSPrefix20210804\phpDocumentor\Reflection\Utils::pregSplit('/\\s+/Su', $body, 2);
        $description = isset($parts[1]) ? $descriptionFactory->create($parts[1], $context) : null;
        // https://tools.ietf.org/html/rfc2396#section-3
        if (\preg_match('/\\w:\\/\\/\\w/i', $parts[0])) {
            return new static(new \ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\Tags\Reference\Url($parts[0]), $description);
        }
        return new static(new \ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\Tags\Reference\Fqsen(self::resolveFqsen($parts[0], $typeResolver, $context)), $description);
    }
    private static function resolveFqsen(string $parts, ?\ECSPrefix20210804\phpDocumentor\Reflection\FqsenResolver $fqsenResolver, ?\ECSPrefix20210804\phpDocumentor\Reflection\Types\Context $context) : \ECSPrefix20210804\phpDocumentor\Reflection\Fqsen
    {
        \ECSPrefix20210804\Webmozart\Assert\Assert::notNull($fqsenResolver);
        $fqsenParts = \explode('::', $parts);
        $resolved = $fqsenResolver->resolve($fqsenParts[0], $context);
        if (!\array_key_exists(1, $fqsenParts)) {
            return $resolved;
        }
        return new \ECSPrefix20210804\phpDocumentor\Reflection\Fqsen($resolved . '::' . $fqsenParts[1]);
    }
    /**
     * Returns the ref of this tag.
     */
    public function getReference() : \ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\Tags\Reference\Reference
    {
        return $this->refers;
    }
    /**
     * Returns a string representation of this tag.
     */
    public function __toString() : string
    {
        if ($this->description) {
            $description = $this->description->render();
        } else {
            $description = '';
        }
        $refers = (string) $this->refers;
        return $refers . ($description !== '' ? ($refers !== '' ? ' ' : '') . $description : '');
    }
}
