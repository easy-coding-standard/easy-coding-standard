<?php

declare (strict_types=1);
namespace ECSPrefix20210804\PhpParser\Builder;

use ECSPrefix20210804\PhpParser;
use ECSPrefix20210804\PhpParser\BuilderHelpers;
use ECSPrefix20210804\PhpParser\Node;
use ECSPrefix20210804\PhpParser\Node\Identifier;
use ECSPrefix20210804\PhpParser\Node\Name;
use ECSPrefix20210804\PhpParser\Node\NullableType;
use ECSPrefix20210804\PhpParser\Node\Stmt;
class Property implements \ECSPrefix20210804\PhpParser\Builder
{
    protected $name;
    protected $flags = 0;
    protected $default = null;
    protected $attributes = [];
    /** @var null|Identifier|Name|NullableType */
    protected $type;
    /** @var Node\AttributeGroup[] */
    protected $attributeGroups = [];
    /**
     * Creates a property builder.
     *
     * @param string $name Name of the property
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }
    /**
     * Makes the property public.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makePublic()
    {
        $this->flags = \ECSPrefix20210804\PhpParser\BuilderHelpers::addModifier($this->flags, \ECSPrefix20210804\PhpParser\Node\Stmt\Class_::MODIFIER_PUBLIC);
        return $this;
    }
    /**
     * Makes the property protected.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makeProtected()
    {
        $this->flags = \ECSPrefix20210804\PhpParser\BuilderHelpers::addModifier($this->flags, \ECSPrefix20210804\PhpParser\Node\Stmt\Class_::MODIFIER_PROTECTED);
        return $this;
    }
    /**
     * Makes the property private.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makePrivate()
    {
        $this->flags = \ECSPrefix20210804\PhpParser\BuilderHelpers::addModifier($this->flags, \ECSPrefix20210804\PhpParser\Node\Stmt\Class_::MODIFIER_PRIVATE);
        return $this;
    }
    /**
     * Makes the property static.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makeStatic()
    {
        $this->flags = \ECSPrefix20210804\PhpParser\BuilderHelpers::addModifier($this->flags, \ECSPrefix20210804\PhpParser\Node\Stmt\Class_::MODIFIER_STATIC);
        return $this;
    }
    /**
     * Makes the property readonly.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makeReadonly()
    {
        $this->flags = \ECSPrefix20210804\PhpParser\BuilderHelpers::addModifier($this->flags, \ECSPrefix20210804\PhpParser\Node\Stmt\Class_::MODIFIER_READONLY);
        return $this;
    }
    /**
     * Sets default value for the property.
     *
     * @param mixed $value Default value to use
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function setDefault($value)
    {
        $this->default = \ECSPrefix20210804\PhpParser\BuilderHelpers::normalizeValue($value);
        return $this;
    }
    /**
     * Sets doc comment for the property.
     *
     * @param PhpParser\Comment\Doc|string $docComment Doc comment to set
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function setDocComment($docComment)
    {
        $this->attributes = ['comments' => [\ECSPrefix20210804\PhpParser\BuilderHelpers::normalizeDocComment($docComment)]];
        return $this;
    }
    /**
     * Sets the property type for PHP 7.4+.
     *
     * @param string|Name|NullableType|Identifier $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = \ECSPrefix20210804\PhpParser\BuilderHelpers::normalizeType($type);
        return $this;
    }
    /**
     * Adds an attribute group.
     *
     * @param Node\Attribute|Node\AttributeGroup $attribute
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function addAttribute($attribute)
    {
        $this->attributeGroups[] = \ECSPrefix20210804\PhpParser\BuilderHelpers::normalizeAttribute($attribute);
        return $this;
    }
    /**
     * Returns the built class node.
     *
     * @return Stmt\Property The built property node
     */
    public function getNode() : \ECSPrefix20210804\PhpParser\Node
    {
        return new \ECSPrefix20210804\PhpParser\Node\Stmt\Property($this->flags !== 0 ? $this->flags : \ECSPrefix20210804\PhpParser\Node\Stmt\Class_::MODIFIER_PUBLIC, [new \ECSPrefix20210804\PhpParser\Node\Stmt\PropertyProperty($this->name, $this->default)], $this->attributes, $this->type, $this->attributeGroups);
    }
}
