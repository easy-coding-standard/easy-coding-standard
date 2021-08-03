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
namespace ECSPrefix20210803\phpDocumentor\Reflection\DocBlock;

use InvalidArgumentException;
use ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Author;
use ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Covers;
use ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Deprecated;
use ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Generic;
use ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\InvalidTag;
use ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Link as LinkTag;
use ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Method;
use ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Param;
use ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Property;
use ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\PropertyRead;
use ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\PropertyWrite;
use ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Return_;
use ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\See as SeeTag;
use ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Since;
use ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Source;
use ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Throws;
use ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Uses;
use ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Var_;
use ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Version;
use ECSPrefix20210803\phpDocumentor\Reflection\FqsenResolver;
use ECSPrefix20210803\phpDocumentor\Reflection\Types\Context as TypeContext;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ECSPrefix20210803\Webmozart\Assert\Assert;
use function array_merge;
use function array_slice;
use function call_user_func_array;
use function count;
use function get_class;
use function preg_match;
use function strpos;
use function trim;
/**
 * Creates a Tag object given the contents of a tag.
 *
 * This Factory is capable of determining the appropriate class for a tag and instantiate it using its `create`
 * factory method. The `create` factory method of a Tag can have a variable number of arguments; this way you can
 * pass the dependencies that you need to construct a tag object.
 *
 * > Important: each parameter in addition to the body variable for the `create` method must default to null, otherwise
 * > it violates the constraint with the interface; it is recommended to use the {@see Assert::notNull()} method to
 * > verify that a dependency is actually passed.
 *
 * This Factory also features a Service Locator component that is used to pass the right dependencies to the
 * `create` method of a tag; each dependency should be registered as a service or as a parameter.
 *
 * When you want to use a Tag of your own with custom handling you need to call the `registerTagHandler` method, pass
 * the name of the tag and a Fully Qualified Class Name pointing to a class that implements the Tag interface.
 */
final class StandardTagFactory implements \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\TagFactory
{
    /** PCRE regular expression matching a tag name. */
    public const REGEX_TAGNAME = '[\\w\\-\\_\\\\:]+';
    /**
     * @var array<class-string<Tag>> An array with a tag as a key, and an
     *                               FQCN to a class that handles it as an array value.
     */
    private $tagHandlerMappings = [
        'author' => \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Author::class,
        'covers' => \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Covers::class,
        'deprecated' => \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Deprecated::class,
        // 'example'        => '\phpDocumentor\Reflection\DocBlock\Tags\Example',
        'link' => \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Link::class,
        'method' => \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Method::class,
        'param' => \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Param::class,
        'property-read' => \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\PropertyRead::class,
        'property' => \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Property::class,
        'property-write' => \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\PropertyWrite::class,
        'return' => \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Return_::class,
        'see' => \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\See::class,
        'since' => \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Since::class,
        'source' => \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Source::class,
        'throw' => \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Throws::class,
        'throws' => \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Throws::class,
        'uses' => \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Uses::class,
        'var' => \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Var_::class,
        'version' => \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Version::class,
    ];
    /**
     * @var array<class-string<Tag>> An array with a anotation s a key, and an
     *      FQCN to a class that handles it as an array value.
     */
    private $annotationMappings = [];
    /**
     * @var ReflectionParameter[][] a lazy-loading cache containing parameters
     *      for each tagHandler that has been used.
     */
    private $tagHandlerParameterCache = [];
    /** @var FqsenResolver */
    private $fqsenResolver;
    /**
     * @var mixed[] an array representing a simple Service Locator where we can store parameters and
     *     services that can be inserted into the Factory Methods of Tag Handlers.
     */
    private $serviceLocator = [];
    /**
     * Initialize this tag factory with the means to resolve an FQSEN and optionally a list of tag handlers.
     *
     * If no tag handlers are provided than the default list in the {@see self::$tagHandlerMappings} property
     * is used.
     *
     * @see self::registerTagHandler() to add a new tag handler to the existing default list.
     *
     * @param array<class-string<Tag>> $tagHandlers
     */
    public function __construct(\ECSPrefix20210803\phpDocumentor\Reflection\FqsenResolver $fqsenResolver, ?array $tagHandlers = null)
    {
        $this->fqsenResolver = $fqsenResolver;
        if ($tagHandlers !== null) {
            $this->tagHandlerMappings = $tagHandlers;
        }
        $this->addService($fqsenResolver, \ECSPrefix20210803\phpDocumentor\Reflection\FqsenResolver::class);
    }
    public function create(string $tagLine, ?\ECSPrefix20210803\phpDocumentor\Reflection\Types\Context $context = null) : \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tag
    {
        if (!$context) {
            $context = new \ECSPrefix20210803\phpDocumentor\Reflection\Types\Context('');
        }
        [$tagName, $tagBody] = $this->extractTagParts($tagLine);
        return $this->createTag(\trim($tagBody), $tagName, $context);
    }
    /**
     * @param mixed $value
     */
    public function addParameter(string $name, $value) : void
    {
        $this->serviceLocator[$name] = $value;
    }
    public function addService(object $service, ?string $alias = null) : void
    {
        $this->serviceLocator[$alias ?: \get_class($service)] = $service;
    }
    public function registerTagHandler(string $tagName, string $handler) : void
    {
        \ECSPrefix20210803\Webmozart\Assert\Assert::stringNotEmpty($tagName);
        \ECSPrefix20210803\Webmozart\Assert\Assert::classExists($handler);
        \ECSPrefix20210803\Webmozart\Assert\Assert::implementsInterface($handler, \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tag::class);
        if (\strpos($tagName, '\\') && $tagName[0] !== '\\') {
            throw new \InvalidArgumentException('A namespaced tag must have a leading backslash as it must be fully qualified');
        }
        $this->tagHandlerMappings[$tagName] = $handler;
    }
    /**
     * Extracts all components for a tag.
     *
     * @return string[]
     */
    private function extractTagParts(string $tagLine) : array
    {
        $matches = [];
        if (!\preg_match('/^@(' . self::REGEX_TAGNAME . ')((?:[\\s\\(\\{])\\s*([^\\s].*)|$)/us', $tagLine, $matches)) {
            throw new \InvalidArgumentException('The tag "' . $tagLine . '" does not seem to be wellformed, please check it for errors');
        }
        if (\count($matches) < 3) {
            $matches[] = '';
        }
        return \array_slice($matches, 1);
    }
    /**
     * Creates a new tag object with the given name and body or returns null if the tag name was recognized but the
     * body was invalid.
     */
    private function createTag(string $body, string $name, \ECSPrefix20210803\phpDocumentor\Reflection\Types\Context $context) : \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tag
    {
        $handlerClassName = $this->findHandlerClassName($name, $context);
        $arguments = $this->getArgumentsForParametersFromWiring($this->fetchParametersForHandlerFactoryMethod($handlerClassName), $this->getServiceLocatorWithDynamicParameters($context, $name, $body));
        try {
            $callable = [$handlerClassName, 'create'];
            \ECSPrefix20210803\Webmozart\Assert\Assert::isCallable($callable);
            /** @phpstan-var callable(string): ?Tag $callable */
            $tag = \call_user_func_array($callable, $arguments);
            return $tag ?? \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\InvalidTag::create($body, $name);
        } catch (\InvalidArgumentException $e) {
            return \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\InvalidTag::create($body, $name)->withError($e);
        }
    }
    /**
     * Determines the Fully Qualified Class Name of the Factory or Tag (containing a Factory Method `create`).
     *
     * @return class-string<Tag>
     */
    private function findHandlerClassName(string $tagName, \ECSPrefix20210803\phpDocumentor\Reflection\Types\Context $context) : string
    {
        $handlerClassName = \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock\Tags\Generic::class;
        if (isset($this->tagHandlerMappings[$tagName])) {
            $handlerClassName = $this->tagHandlerMappings[$tagName];
        } elseif ($this->isAnnotation($tagName)) {
            // TODO: Annotation support is planned for a later stage and as such is disabled for now
            $tagName = (string) $this->fqsenResolver->resolve($tagName, $context);
            if (isset($this->annotationMappings[$tagName])) {
                $handlerClassName = $this->annotationMappings[$tagName];
            }
        }
        return $handlerClassName;
    }
    /**
     * Retrieves the arguments that need to be passed to the Factory Method with the given Parameters.
     *
     * @param ReflectionParameter[] $parameters
     * @param mixed[]               $locator
     *
     * @return mixed[] A series of values that can be passed to the Factory Method of the tag whose parameters
     *     is provided with this method.
     */
    private function getArgumentsForParametersFromWiring(array $parameters, array $locator) : array
    {
        $arguments = [];
        foreach ($parameters as $parameter) {
            $type = $parameter->getType();
            $typeHint = null;
            if ($type instanceof \ReflectionNamedType) {
                $typeHint = $type->getName();
                if ($typeHint === 'self') {
                    $declaringClass = $parameter->getDeclaringClass();
                    if ($declaringClass !== null) {
                        $typeHint = $declaringClass->getName();
                    }
                }
            }
            if (isset($locator[$typeHint])) {
                $arguments[] = $locator[$typeHint];
                continue;
            }
            $parameterName = $parameter->getName();
            if (isset($locator[$parameterName])) {
                $arguments[] = $locator[$parameterName];
                continue;
            }
            $arguments[] = null;
        }
        return $arguments;
    }
    /**
     * Retrieves a series of ReflectionParameter objects for the static 'create' method of the given
     * tag handler class name.
     *
     * @param class-string $handlerClassName
     *
     * @return ReflectionParameter[]
     */
    private function fetchParametersForHandlerFactoryMethod(string $handlerClassName) : array
    {
        if (!isset($this->tagHandlerParameterCache[$handlerClassName])) {
            $methodReflection = new \ReflectionMethod($handlerClassName, 'create');
            $this->tagHandlerParameterCache[$handlerClassName] = $methodReflection->getParameters();
        }
        return $this->tagHandlerParameterCache[$handlerClassName];
    }
    /**
     * Returns a copy of this class' Service Locator with added dynamic parameters,
     * such as the tag's name, body and Context.
     *
     * @param TypeContext $context The Context (namespace and aliasses) that may be
     *  passed and is used to resolve FQSENs.
     * @param string      $tagName The name of the tag that may be
     *  passed onto the factory method of the Tag class.
     * @param string      $tagBody The body of the tag that may be
     *  passed onto the factory method of the Tag class.
     *
     * @return mixed[]
     */
    private function getServiceLocatorWithDynamicParameters(\ECSPrefix20210803\phpDocumentor\Reflection\Types\Context $context, string $tagName, string $tagBody) : array
    {
        return \array_merge($this->serviceLocator, ['name' => $tagName, 'body' => $tagBody, \ECSPrefix20210803\phpDocumentor\Reflection\Types\Context::class => $context]);
    }
    /**
     * Returns whether the given tag belongs to an annotation.
     *
     * @todo this method should be populated once we implement Annotation notation support.
     */
    private function isAnnotation(string $tagContent) : bool
    {
        // 1. Contains a namespace separator
        // 2. Contains parenthesis
        // 3. Is present in a list of known annotations (make the algorithm smart by first checking is the last part
        //    of the annotation class name matches the found tag name
        return \false;
    }
}
