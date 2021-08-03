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
namespace ECSPrefix20210803\phpDocumentor\Reflection;

use ArrayIterator;
use InvalidArgumentException;
use ECSPrefix20210803\phpDocumentor\Reflection\Types\Array_;
use ECSPrefix20210803\phpDocumentor\Reflection\Types\ClassString;
use ECSPrefix20210803\phpDocumentor\Reflection\Types\Collection;
use ECSPrefix20210803\phpDocumentor\Reflection\Types\Compound;
use ECSPrefix20210803\phpDocumentor\Reflection\Types\Context;
use ECSPrefix20210803\phpDocumentor\Reflection\Types\Expression;
use ECSPrefix20210803\phpDocumentor\Reflection\Types\Integer;
use ECSPrefix20210803\phpDocumentor\Reflection\Types\Intersection;
use ECSPrefix20210803\phpDocumentor\Reflection\Types\Iterable_;
use ECSPrefix20210803\phpDocumentor\Reflection\Types\Nullable;
use ECSPrefix20210803\phpDocumentor\Reflection\Types\Object_;
use ECSPrefix20210803\phpDocumentor\Reflection\Types\String_;
use RuntimeException;
use function array_key_exists;
use function array_pop;
use function array_values;
use function class_exists;
use function class_implements;
use function count;
use function end;
use function in_array;
use function key;
use function preg_split;
use function strpos;
use function strtolower;
use function trim;
use const PREG_SPLIT_DELIM_CAPTURE;
use const PREG_SPLIT_NO_EMPTY;
final class TypeResolver
{
    /** @var string Definition of the ARRAY operator for types */
    private const OPERATOR_ARRAY = '[]';
    /** @var string Definition of the NAMESPACE operator in PHP */
    private const OPERATOR_NAMESPACE = '\\';
    /** @var int the iterator parser is inside a compound context */
    private const PARSER_IN_COMPOUND = 0;
    /** @var int the iterator parser is inside a nullable expression context */
    private const PARSER_IN_NULLABLE = 1;
    /** @var int the iterator parser is inside an array expression context */
    private const PARSER_IN_ARRAY_EXPRESSION = 2;
    /** @var int the iterator parser is inside a collection expression context */
    private const PARSER_IN_COLLECTION_EXPRESSION = 3;
    /**
     * @var array<string, string> List of recognized keywords and unto which Value Object they map
     * @psalm-var array<string, class-string<Type>>
     */
    private $keywords = ['string' => \ECSPrefix20210803\phpDocumentor\Reflection\Types\String_::class, 'class-string' => \ECSPrefix20210803\phpDocumentor\Reflection\Types\ClassString::class, 'int' => \ECSPrefix20210803\phpDocumentor\Reflection\Types\Integer::class, 'integer' => \ECSPrefix20210803\phpDocumentor\Reflection\Types\Integer::class, 'bool' => \ECSPrefix20210803\phpDocumentor\Reflection\Types\Boolean::class, 'boolean' => \ECSPrefix20210803\phpDocumentor\Reflection\Types\Boolean::class, 'real' => \ECSPrefix20210803\phpDocumentor\Reflection\Types\Float_::class, 'float' => \ECSPrefix20210803\phpDocumentor\Reflection\Types\Float_::class, 'double' => \ECSPrefix20210803\phpDocumentor\Reflection\Types\Float_::class, 'object' => \ECSPrefix20210803\phpDocumentor\Reflection\Types\Object_::class, 'mixed' => \ECSPrefix20210803\phpDocumentor\Reflection\Types\Mixed_::class, 'array' => \ECSPrefix20210803\phpDocumentor\Reflection\Types\Array_::class, 'resource' => \ECSPrefix20210803\phpDocumentor\Reflection\Types\Resource_::class, 'void' => \ECSPrefix20210803\phpDocumentor\Reflection\Types\Void_::class, 'null' => \ECSPrefix20210803\phpDocumentor\Reflection\Types\Null_::class, 'scalar' => \ECSPrefix20210803\phpDocumentor\Reflection\Types\Scalar::class, 'callback' => \ECSPrefix20210803\phpDocumentor\Reflection\Types\Callable_::class, 'callable' => \ECSPrefix20210803\phpDocumentor\Reflection\Types\Callable_::class, 'false' => \ECSPrefix20210803\phpDocumentor\Reflection\PseudoTypes\False_::class, 'true' => \ECSPrefix20210803\phpDocumentor\Reflection\PseudoTypes\True_::class, 'self' => \ECSPrefix20210803\phpDocumentor\Reflection\Types\Self_::class, '$this' => \ECSPrefix20210803\phpDocumentor\Reflection\Types\This::class, 'static' => \ECSPrefix20210803\phpDocumentor\Reflection\Types\Static_::class, 'parent' => \ECSPrefix20210803\phpDocumentor\Reflection\Types\Parent_::class, 'iterable' => \ECSPrefix20210803\phpDocumentor\Reflection\Types\Iterable_::class];
    /**
     * @var FqsenResolver
     * @psalm-readonly
     */
    private $fqsenResolver;
    /**
     * Initializes this TypeResolver with the means to create and resolve Fqsen objects.
     */
    public function __construct(?\ECSPrefix20210803\phpDocumentor\Reflection\FqsenResolver $fqsenResolver = null)
    {
        $this->fqsenResolver = $fqsenResolver ?: new \ECSPrefix20210803\phpDocumentor\Reflection\FqsenResolver();
    }
    /**
     * Analyzes the given type and returns the FQCN variant.
     *
     * When a type is provided this method checks whether it is not a keyword or
     * Fully Qualified Class Name. If so it will use the given namespace and
     * aliases to expand the type to a FQCN representation.
     *
     * This method only works as expected if the namespace and aliases are set;
     * no dynamic reflection is being performed here.
     *
     * @uses Context::getNamespaceAliases() to check whether the first part of the relative type name should not be
     * replaced with another namespace.
     * @uses Context::getNamespace()        to determine with what to prefix the type name.
     *
     * @param string $type The relative or absolute type.
     */
    public function resolve(string $type, ?\ECSPrefix20210803\phpDocumentor\Reflection\Types\Context $context = null) : \ECSPrefix20210803\phpDocumentor\Reflection\Type
    {
        $type = \trim($type);
        if (!$type) {
            throw new \InvalidArgumentException('Attempted to resolve "' . $type . '" but it appears to be empty');
        }
        if ($context === null) {
            $context = new \ECSPrefix20210803\phpDocumentor\Reflection\Types\Context('');
        }
        // split the type string into tokens `|`, `?`, `<`, `>`, `,`, `(`, `)`, `[]`, '<', '>' and type names
        $tokens = \preg_split('/(\\||\\?|<|>|&|, ?|\\(|\\)|\\[\\]+)/', $type, -1, \PREG_SPLIT_NO_EMPTY | \PREG_SPLIT_DELIM_CAPTURE);
        if ($tokens === \false) {
            throw new \InvalidArgumentException('Unable to split the type string "' . $type . '" into tokens');
        }
        /** @var ArrayIterator<int, string|null> $tokenIterator */
        $tokenIterator = new \ArrayIterator($tokens);
        return $this->parseTypes($tokenIterator, $context, self::PARSER_IN_COMPOUND);
    }
    /**
     * Analyse each tokens and creates types
     *
     * @param ArrayIterator<int, string|null> $tokens        the iterator on tokens
     * @param int                        $parserContext on of self::PARSER_* constants, indicating
     * the context where we are in the parsing
     */
    private function parseTypes(\ArrayIterator $tokens, \ECSPrefix20210803\phpDocumentor\Reflection\Types\Context $context, int $parserContext) : \ECSPrefix20210803\phpDocumentor\Reflection\Type
    {
        $types = [];
        $token = '';
        $compoundToken = '|';
        while ($tokens->valid()) {
            $token = $tokens->current();
            if ($token === null) {
                throw new \RuntimeException('Unexpected nullable character');
            }
            if ($token === '|' || $token === '&') {
                if (\count($types) === 0) {
                    throw new \RuntimeException('A type is missing before a type separator');
                }
                if (!\in_array($parserContext, [self::PARSER_IN_COMPOUND, self::PARSER_IN_ARRAY_EXPRESSION, self::PARSER_IN_COLLECTION_EXPRESSION], \true)) {
                    throw new \RuntimeException('Unexpected type separator');
                }
                $compoundToken = $token;
                $tokens->next();
            } elseif ($token === '?') {
                if (!\in_array($parserContext, [self::PARSER_IN_COMPOUND, self::PARSER_IN_ARRAY_EXPRESSION, self::PARSER_IN_COLLECTION_EXPRESSION], \true)) {
                    throw new \RuntimeException('Unexpected nullable character');
                }
                $tokens->next();
                $type = $this->parseTypes($tokens, $context, self::PARSER_IN_NULLABLE);
                $types[] = new \ECSPrefix20210803\phpDocumentor\Reflection\Types\Nullable($type);
            } elseif ($token === '(') {
                $tokens->next();
                $type = $this->parseTypes($tokens, $context, self::PARSER_IN_ARRAY_EXPRESSION);
                $token = $tokens->current();
                if ($token === null) {
                    // Someone did not properly close their array expression ..
                    break;
                }
                $tokens->next();
                $resolvedType = new \ECSPrefix20210803\phpDocumentor\Reflection\Types\Expression($type);
                $types[] = $resolvedType;
            } elseif ($parserContext === self::PARSER_IN_ARRAY_EXPRESSION && $token[0] === ')') {
                break;
            } elseif ($token === '<') {
                if (\count($types) === 0) {
                    throw new \RuntimeException('Unexpected collection operator "<", class name is missing');
                }
                $classType = \array_pop($types);
                if ($classType !== null) {
                    if ((string) $classType === 'class-string') {
                        $types[] = $this->resolveClassString($tokens, $context);
                    } else {
                        $types[] = $this->resolveCollection($tokens, $classType, $context);
                    }
                }
                $tokens->next();
            } elseif ($parserContext === self::PARSER_IN_COLLECTION_EXPRESSION && ($token === '>' || \trim($token) === ',')) {
                break;
            } elseif ($token === self::OPERATOR_ARRAY) {
                \end($types);
                $last = \key($types);
                $lastItem = $types[$last];
                if ($lastItem instanceof \ECSPrefix20210803\phpDocumentor\Reflection\Types\Expression) {
                    $lastItem = $lastItem->getValueType();
                }
                $types[$last] = new \ECSPrefix20210803\phpDocumentor\Reflection\Types\Array_($lastItem);
                $tokens->next();
            } else {
                $type = $this->resolveSingleType($token, $context);
                $tokens->next();
                if ($parserContext === self::PARSER_IN_NULLABLE) {
                    return $type;
                }
                $types[] = $type;
            }
        }
        if ($token === '|' || $token === '&') {
            throw new \RuntimeException('A type is missing after a type separator');
        }
        if (\count($types) === 0) {
            if ($parserContext === self::PARSER_IN_NULLABLE) {
                throw new \RuntimeException('A type is missing after a nullable character');
            }
            if ($parserContext === self::PARSER_IN_ARRAY_EXPRESSION) {
                throw new \RuntimeException('A type is missing in an array expression');
            }
            if ($parserContext === self::PARSER_IN_COLLECTION_EXPRESSION) {
                throw new \RuntimeException('A type is missing in a collection expression');
            }
        } elseif (\count($types) === 1) {
            return $types[0];
        }
        if ($compoundToken === '|') {
            return new \ECSPrefix20210803\phpDocumentor\Reflection\Types\Compound(\array_values($types));
        }
        return new \ECSPrefix20210803\phpDocumentor\Reflection\Types\Intersection(\array_values($types));
    }
    /**
     * resolve the given type into a type object
     *
     * @param string $type the type string, representing a single type
     *
     * @return Type|Array_|Object_
     *
     * @psalm-mutation-free
     */
    private function resolveSingleType(string $type, \ECSPrefix20210803\phpDocumentor\Reflection\Types\Context $context) : object
    {
        switch (\true) {
            case $this->isKeyword($type):
                return $this->resolveKeyword($type);
            case $this->isFqsen($type):
                return $this->resolveTypedObject($type);
            case $this->isPartialStructuralElementName($type):
                return $this->resolveTypedObject($type, $context);
            // @codeCoverageIgnoreStart
            default:
                // I haven't got the foggiest how the logic would come here but added this as a defense.
                throw new \RuntimeException('Unable to resolve type "' . $type . '", there is no known method to resolve it');
        }
        // @codeCoverageIgnoreEnd
    }
    /**
     * Adds a keyword to the list of Keywords and associates it with a specific Value Object.
     *
     * @psalm-param class-string<Type> $typeClassName
     */
    public function addKeyword(string $keyword, string $typeClassName) : void
    {
        if (!\class_exists($typeClassName)) {
            throw new \InvalidArgumentException('The Value Object that needs to be created with a keyword "' . $keyword . '" must be an existing class' . ' but we could not find the class ' . $typeClassName);
        }
        if (!\in_array(\ECSPrefix20210803\phpDocumentor\Reflection\Type::class, \class_implements($typeClassName), \true)) {
            throw new \InvalidArgumentException('The class "' . $typeClassName . '" must implement the interface "phpDocumentor\\Reflection\\Type"');
        }
        $this->keywords[$keyword] = $typeClassName;
    }
    /**
     * Detects whether the given type represents a PHPDoc keyword.
     *
     * @param string $type A relative or absolute type as defined in the phpDocumentor documentation.
     *
     * @psalm-mutation-free
     */
    private function isKeyword(string $type) : bool
    {
        return \array_key_exists(\strtolower($type), $this->keywords);
    }
    /**
     * Detects whether the given type represents a relative structural element name.
     *
     * @param string $type A relative or absolute type as defined in the phpDocumentor documentation.
     *
     * @psalm-mutation-free
     */
    private function isPartialStructuralElementName(string $type) : bool
    {
        return $type[0] !== self::OPERATOR_NAMESPACE && !$this->isKeyword($type);
    }
    /**
     * Tests whether the given type is a Fully Qualified Structural Element Name.
     *
     * @psalm-mutation-free
     */
    private function isFqsen(string $type) : bool
    {
        return \strpos($type, self::OPERATOR_NAMESPACE) === 0;
    }
    /**
     * Resolves the given keyword (such as `string`) into a Type object representing that keyword.
     *
     * @psalm-mutation-free
     */
    private function resolveKeyword(string $type) : \ECSPrefix20210803\phpDocumentor\Reflection\Type
    {
        $className = $this->keywords[\strtolower($type)];
        return new $className();
    }
    /**
     * Resolves the given FQSEN string into an FQSEN object.
     *
     * @psalm-mutation-free
     */
    private function resolveTypedObject(string $type, ?\ECSPrefix20210803\phpDocumentor\Reflection\Types\Context $context = null) : \ECSPrefix20210803\phpDocumentor\Reflection\Types\Object_
    {
        return new \ECSPrefix20210803\phpDocumentor\Reflection\Types\Object_($this->fqsenResolver->resolve($type, $context));
    }
    /**
     * Resolves class string
     *
     * @param ArrayIterator<int, (string|null)> $tokens
     */
    private function resolveClassString(\ArrayIterator $tokens, \ECSPrefix20210803\phpDocumentor\Reflection\Types\Context $context) : \ECSPrefix20210803\phpDocumentor\Reflection\Type
    {
        $tokens->next();
        $classType = $this->parseTypes($tokens, $context, self::PARSER_IN_COLLECTION_EXPRESSION);
        if (!$classType instanceof \ECSPrefix20210803\phpDocumentor\Reflection\Types\Object_ || $classType->getFqsen() === null) {
            throw new \RuntimeException($classType . ' is not a class string');
        }
        $token = $tokens->current();
        if ($token !== '>') {
            if (empty($token)) {
                throw new \RuntimeException('class-string: ">" is missing');
            }
            throw new \RuntimeException('Unexpected character "' . $token . '", ">" is missing');
        }
        return new \ECSPrefix20210803\phpDocumentor\Reflection\Types\ClassString($classType->getFqsen());
    }
    /**
     * Resolves the collection values and keys
     *
     * @param ArrayIterator<int, (string|null)> $tokens
     *
     * @return Array_|Iterable_|Collection
     */
    private function resolveCollection(\ArrayIterator $tokens, \ECSPrefix20210803\phpDocumentor\Reflection\Type $classType, \ECSPrefix20210803\phpDocumentor\Reflection\Types\Context $context) : \ECSPrefix20210803\phpDocumentor\Reflection\Type
    {
        $isArray = (string) $classType === 'array';
        $isIterable = (string) $classType === 'iterable';
        // allow only "array", "iterable" or class name before "<"
        if (!$isArray && !$isIterable && (!$classType instanceof \ECSPrefix20210803\phpDocumentor\Reflection\Types\Object_ || $classType->getFqsen() === null)) {
            throw new \RuntimeException($classType . ' is not a collection');
        }
        $tokens->next();
        $valueType = $this->parseTypes($tokens, $context, self::PARSER_IN_COLLECTION_EXPRESSION);
        $keyType = null;
        $token = $tokens->current();
        if ($token !== null && \trim($token) === ',') {
            // if we have a comma, then we just parsed the key type, not the value type
            $keyType = $valueType;
            if ($isArray) {
                // check the key type for an "array" collection. We allow only
                // strings or integers.
                if (!$keyType instanceof \ECSPrefix20210803\phpDocumentor\Reflection\Types\String_ && !$keyType instanceof \ECSPrefix20210803\phpDocumentor\Reflection\Types\Integer && !$keyType instanceof \ECSPrefix20210803\phpDocumentor\Reflection\Types\Compound) {
                    throw new \RuntimeException('An array can have only integers or strings as keys');
                }
                if ($keyType instanceof \ECSPrefix20210803\phpDocumentor\Reflection\Types\Compound) {
                    foreach ($keyType->getIterator() as $item) {
                        if (!$item instanceof \ECSPrefix20210803\phpDocumentor\Reflection\Types\String_ && !$item instanceof \ECSPrefix20210803\phpDocumentor\Reflection\Types\Integer) {
                            throw new \RuntimeException('An array can have only integers or strings as keys');
                        }
                    }
                }
            }
            $tokens->next();
            // now let's parse the value type
            $valueType = $this->parseTypes($tokens, $context, self::PARSER_IN_COLLECTION_EXPRESSION);
        }
        $token = $tokens->current();
        if ($token !== '>') {
            if (empty($token)) {
                throw new \RuntimeException('Collection: ">" is missing');
            }
            throw new \RuntimeException('Unexpected character "' . $token . '", ">" is missing');
        }
        if ($isArray) {
            return new \ECSPrefix20210803\phpDocumentor\Reflection\Types\Array_($valueType, $keyType);
        }
        if ($isIterable) {
            return new \ECSPrefix20210803\phpDocumentor\Reflection\Types\Iterable_($valueType, $keyType);
        }
        if ($classType instanceof \ECSPrefix20210803\phpDocumentor\Reflection\Types\Object_) {
            return $this->makeCollectionFromObject($classType, $valueType, $keyType);
        }
        throw new \RuntimeException('Invalid $classType provided');
    }
    /**
     * @psalm-pure
     */
    private function makeCollectionFromObject(\ECSPrefix20210803\phpDocumentor\Reflection\Types\Object_ $object, \ECSPrefix20210803\phpDocumentor\Reflection\Type $valueType, ?\ECSPrefix20210803\phpDocumentor\Reflection\Type $keyType = null) : \ECSPrefix20210803\phpDocumentor\Reflection\Types\Collection
    {
        return new \ECSPrefix20210803\phpDocumentor\Reflection\Types\Collection($object->getFqsen(), $valueType, $keyType);
    }
}
