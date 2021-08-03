<?php

declare (strict_types=1);
namespace ECSPrefix20210803\phpDocumentor\Reflection;

use ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tag;
// phpcs:ignore SlevomatCodingStandard.Classes.SuperfluousInterfaceNaming.SuperfluousSuffix
interface DocBlockFactoryInterface
{
    /**
     * Factory method for easy instantiation.
     *
     * @param array<string, class-string<Tag>> $additionalTags
     */
    public static function createInstance(array $additionalTags = []) : \ECSPrefix20210803\phpDocumentor\Reflection\DocBlockFactory;
    /**
     * @param string|object $docblock
     */
    public function create($docblock, ?\ECSPrefix20210803\phpDocumentor\Reflection\Types\Context $context = null, ?\ECSPrefix20210803\phpDocumentor\Reflection\Location $location = null) : \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock;
}
