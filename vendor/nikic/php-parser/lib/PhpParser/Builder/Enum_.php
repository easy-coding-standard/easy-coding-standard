<?php

declare (strict_types=1);
namespace ECSPrefix202608\PhpParser\Builder;

use ECSPrefix202608\PhpParser;
use ECSPrefix202608\PhpParser\BuilderHelpers;
use ECSPrefix202608\PhpParser\Node;
use ECSPrefix202608\PhpParser\Node\Identifier;
use ECSPrefix202608\PhpParser\Node\Name;
use ECSPrefix202608\PhpParser\Node\Stmt;
class Enum_ extends Declaration
{
    /**
     * @var string
     */
    protected $name;
    /**
     * @var \PhpParser\Node\Identifier|null
     */
    protected $scalarType;
    /** @var list<Name> */
    protected $implements = [];
    /** @var list<Stmt\TraitUse> */
    protected $uses = [];
    /** @var list<Stmt\EnumCase> */
    protected $enumCases = [];
    /** @var list<Stmt\ClassConst> */
    protected $constants = [];
    /** @var list<Stmt\ClassMethod> */
    protected $methods = [];
    /** @var list<Node\AttributeGroup> */
    protected $attributeGroups = [];
    /**
     * Creates an enum builder.
     *
     * @param string $name Name of the enum
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }
    /**
     * Sets the scalar type.
     *
     * @param string|Identifier $scalarType
     *
     * @return $this
     */
    public function setScalarType($scalarType)
    {
        $this->scalarType = BuilderHelpers::normalizeType($scalarType);
        return $this;
    }
    /**
     * Implements one or more interfaces.
     *
     * @param Name|string ...$interfaces Names of interfaces to implement
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function implement(...$interfaces)
    {
        foreach ($interfaces as $interface) {
            $this->implements[] = BuilderHelpers::normalizeName($interface);
        }
        return $this;
    }
    /**
     * Adds a statement.
     *
     * @param Stmt|PhpParser\Builder $stmt The statement to add
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function addStmt($stmt)
    {
        $stmt = BuilderHelpers::normalizeNode($stmt);
        if ($stmt instanceof Stmt\EnumCase) {
            $this->enumCases[] = $stmt;
        } elseif ($stmt instanceof Stmt\ClassMethod) {
            $this->methods[] = $stmt;
        } elseif ($stmt instanceof Stmt\TraitUse) {
            $this->uses[] = $stmt;
        } elseif ($stmt instanceof Stmt\ClassConst) {
            $this->constants[] = $stmt;
        } else {
            throw new \LogicException(sprintf('Unexpected node of type "%s"', $stmt->getType()));
        }
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
     * Returns the built class node.
     *
     * @return Stmt\Enum_ The built enum node
     */
    public function getNode(): PhpParser\Node
    {
        return new Stmt\Enum_($this->name, ['scalarType' => $this->scalarType, 'implements' => $this->implements, 'stmts' => array_merge($this->uses, $this->enumCases, $this->constants, $this->methods), 'attrGroups' => $this->attributeGroups], $this->attributes);
    }
}
