<?php

declare (strict_types=1);
namespace ECSPrefix202607\PhpParser\Builder;

use ECSPrefix202607\PhpParser;
use ECSPrefix202607\PhpParser\BuilderHelpers;
use ECSPrefix202607\PhpParser\Modifiers;
use ECSPrefix202607\PhpParser\Node;
use ECSPrefix202607\PhpParser\Node\Identifier;
use ECSPrefix202607\PhpParser\Node\Name;
use ECSPrefix202607\PhpParser\Node\Stmt;
use ECSPrefix202607\PhpParser\Node\ComplexType;
class Property implements PhpParser\Builder
{
    /**
     * @var string
     */
    protected $name;
    /**
     * @var int
     */
    protected $flags = 0;
    /**
     * @var \PhpParser\Node\Expr|null
     */
    protected $default;
    /** @var array<string, mixed> */
    protected $attributes = [];
    /** @var null|Identifier|Name|ComplexType */
    protected $type;
    /** @var list<Node\AttributeGroup> */
    protected $attributeGroups = [];
    /** @var list<Node\PropertyHook> */
    protected $hooks = [];
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
        $this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::PUBLIC);
        return $this;
    }
    /**
     * Makes the property protected.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makeProtected()
    {
        $this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::PROTECTED);
        return $this;
    }
    /**
     * Makes the property private.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makePrivate()
    {
        $this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::PRIVATE);
        return $this;
    }
    /**
     * Makes the property static.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makeStatic()
    {
        $this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::STATIC);
        return $this;
    }
    /**
     * Makes the property readonly.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makeReadonly()
    {
        $this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::READONLY);
        return $this;
    }
    /**
     * Makes the property abstract. Requires at least one property hook to be specified as well.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makeAbstract()
    {
        $this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::ABSTRACT);
        return $this;
    }
    /**
     * Makes the property final.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makeFinal()
    {
        $this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::FINAL);
        return $this;
    }
    /**
     * Gives the property private(set) visibility.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makePrivateSet()
    {
        $this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::PRIVATE_SET);
        return $this;
    }
    /**
     * Gives the property protected(set) visibility.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makeProtectedSet()
    {
        $this->flags = BuilderHelpers::addModifier($this->flags, Modifiers::PROTECTED_SET);
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
        $this->default = BuilderHelpers::normalizeValue($value);
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
        $this->attributes = ['comments' => [BuilderHelpers::normalizeDocComment($docComment)]];
        return $this;
    }
    /**
     * Sets the property type for PHP 7.4+.
     *
     * @param string|Name|Identifier|ComplexType $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = BuilderHelpers::normalizeType($type);
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
        $this->attributeGroups[] = BuilderHelpers::normalizeAttribute($attribute);
        return $this;
    }
    /**
     * Adds a property hook.
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function addHook(Node\PropertyHook $hook)
    {
        $this->hooks[] = $hook;
        return $this;
    }
    /**
     * Returns the built class node.
     *
     * @return Stmt\Property The built property node
     */
    public function getNode(): PhpParser\Node
    {
        if ($this->flags & Modifiers::ABSTRACT && !$this->hooks) {
            throw new PhpParser\Error('Only hooked properties may be declared abstract');
        }
        return new Stmt\Property($this->flags !== 0 ? $this->flags : Modifiers::PUBLIC, [new Node\PropertyItem($this->name, $this->default)], $this->attributes, $this->type, $this->attributeGroups, $this->hooks);
    }
}
