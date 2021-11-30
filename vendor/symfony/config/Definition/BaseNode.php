<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20211130\Symfony\Component\Config\Definition;

use ECSPrefix20211130\Symfony\Component\Config\Definition\Exception\Exception;
use ECSPrefix20211130\Symfony\Component\Config\Definition\Exception\ForbiddenOverwriteException;
use ECSPrefix20211130\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use ECSPrefix20211130\Symfony\Component\Config\Definition\Exception\InvalidTypeException;
use ECSPrefix20211130\Symfony\Component\Config\Definition\Exception\UnsetKeyException;
/**
 * The base node class.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
abstract class BaseNode implements \ECSPrefix20211130\Symfony\Component\Config\Definition\NodeInterface
{
    public const DEFAULT_PATH_SEPARATOR = '.';
    /**
     * @var mixed[]
     */
    private static $placeholderUniquePrefixes = [];
    /**
     * @var mixed[]
     */
    private static $placeholders = [];
    protected $name;
    protected $parent;
    protected $normalizationClosures = [];
    protected $finalValidationClosures = [];
    protected $allowOverwrite = \true;
    protected $required = \false;
    protected $deprecation = [];
    protected $equivalentValues = [];
    protected $attributes = [];
    protected $pathSeparator;
    /**
     * @var mixed
     */
    private $handlingPlaceholder = null;
    /**
     * @throws \InvalidArgumentException if the name contains a period
     */
    public function __construct(?string $name, \ECSPrefix20211130\Symfony\Component\Config\Definition\NodeInterface $parent = null, string $pathSeparator = self::DEFAULT_PATH_SEPARATOR)
    {
        if (\strpos($name = (string) $name, $pathSeparator) !== \false) {
            throw new \InvalidArgumentException('The name must not contain ".' . $pathSeparator . '".');
        }
        $this->name = $name;
        $this->parent = $parent;
        $this->pathSeparator = $pathSeparator;
    }
    /**
     * Register possible (dummy) values for a dynamic placeholder value.
     *
     * Matching configuration values will be processed with a provided value, one by one. After a provided value is
     * successfully processed the configuration value is returned as is, thus preserving the placeholder.
     *
     * @internal
     * @param string $placeholder
     * @param mixed[] $values
     */
    public static function setPlaceholder($placeholder, $values) : void
    {
        if (!$values) {
            throw new \InvalidArgumentException('At least one value must be provided.');
        }
        self::$placeholders[$placeholder] = $values;
    }
    /**
     * Adds a common prefix for dynamic placeholder values.
     *
     * Matching configuration values will be skipped from being processed and are returned as is, thus preserving the
     * placeholder. An exact match provided by {@see setPlaceholder()} might take precedence.
     *
     * @internal
     * @param string $prefix
     */
    public static function setPlaceholderUniquePrefix($prefix) : void
    {
        self::$placeholderUniquePrefixes[] = $prefix;
    }
    /**
     * Resets all current placeholders available.
     *
     * @internal
     */
    public static function resetPlaceholders() : void
    {
        self::$placeholderUniquePrefixes = [];
        self::$placeholders = [];
    }
    /**
     * @param mixed $value
     * @param string $key
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }
    /**
     * @param mixed $default
     * @return mixed
     * @param string $key
     */
    public function getAttribute($key, $default = null)
    {
        return $this->attributes[$key] ?? $default;
    }
    /**
     * @param string $key
     */
    public function hasAttribute($key) : bool
    {
        return isset($this->attributes[$key]);
    }
    public function getAttributes() : array
    {
        return $this->attributes;
    }
    /**
     * @param mixed[] $attributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }
    /**
     * @param string $key
     */
    public function removeAttribute($key)
    {
        unset($this->attributes[$key]);
    }
    /**
     * Sets an info message.
     * @param string $info
     */
    public function setInfo($info)
    {
        $this->setAttribute('info', $info);
    }
    /**
     * Returns info message.
     */
    public function getInfo() : ?string
    {
        return $this->getAttribute('info');
    }
    /**
     * Sets the example configuration for this node.
     * @param mixed[]|string $example
     */
    public function setExample($example)
    {
        $this->setAttribute('example', $example);
    }
    /**
     * Retrieves the example configuration for this node.
     * @return mixed[]|string|null
     */
    public function getExample()
    {
        return $this->getAttribute('example');
    }
    /**
     * Adds an equivalent value.
     * @param mixed $originalValue
     * @param mixed $equivalentValue
     */
    public function addEquivalentValue($originalValue, $equivalentValue)
    {
        $this->equivalentValues[] = [$originalValue, $equivalentValue];
    }
    /**
     * Set this node as required.
     * @param bool $boolean
     */
    public function setRequired($boolean)
    {
        $this->required = $boolean;
    }
    /**
     * Sets this node as deprecated.
     *
     * @param string $package The name of the composer package that is triggering the deprecation
     * @param string $version The version of the package that introduced the deprecation
     * @param string $message the deprecation message to use
     *
     * You can use %node% and %path% placeholders in your message to display,
     * respectively, the node name and its complete path
     */
    public function setDeprecated($package, $version, $message = 'The child node "%node%" at path "%path%" is deprecated.')
    {
        $this->deprecation = ['package' => $package, 'version' => $version, 'message' => $message];
    }
    /**
     * Sets if this node can be overridden.
     * @param bool $allow
     */
    public function setAllowOverwrite($allow)
    {
        $this->allowOverwrite = $allow;
    }
    /**
     * Sets the closures used for normalization.
     *
     * @param \Closure[] $closures An array of Closures used for normalization
     */
    public function setNormalizationClosures($closures)
    {
        $this->normalizationClosures = $closures;
    }
    /**
     * Sets the closures used for final validation.
     *
     * @param \Closure[] $closures An array of Closures used for final validation
     */
    public function setFinalValidationClosures($closures)
    {
        $this->finalValidationClosures = $closures;
    }
    /**
     * {@inheritdoc}
     */
    public function isRequired() : bool
    {
        return $this->required;
    }
    /**
     * Checks if this node is deprecated.
     */
    public function isDeprecated() : bool
    {
        return (bool) $this->deprecation;
    }
    /**
     * @param string $node The configuration node name
     * @param string $path The path of the node
     */
    public function getDeprecation($node, $path) : array
    {
        return ['package' => $this->deprecation['package'], 'version' => $this->deprecation['version'], 'message' => \strtr($this->deprecation['message'], ['%node%' => $node, '%path%' => $path])];
    }
    /**
     * {@inheritdoc}
     */
    public function getName() : string
    {
        return $this->name;
    }
    /**
     * {@inheritdoc}
     */
    public function getPath() : string
    {
        if (null !== $this->parent) {
            return $this->parent->getPath() . $this->pathSeparator . $this->name;
        }
        return $this->name;
    }
    /**
     * {@inheritdoc}
     * @param mixed $leftSide
     * @param mixed $rightSide
     * @return mixed
     */
    public final function merge($leftSide, $rightSide)
    {
        if (!$this->allowOverwrite) {
            throw new \ECSPrefix20211130\Symfony\Component\Config\Definition\Exception\ForbiddenOverwriteException(\sprintf('Configuration path "%s" cannot be overwritten. You have to define all options for this path, and any of its sub-paths in one configuration section.', $this->getPath()));
        }
        if ($leftSide !== ($leftPlaceholders = self::resolvePlaceholderValue($leftSide))) {
            foreach ($leftPlaceholders as $leftPlaceholder) {
                $this->handlingPlaceholder = $leftSide;
                try {
                    $this->merge($leftPlaceholder, $rightSide);
                } finally {
                    $this->handlingPlaceholder = null;
                }
            }
            return $rightSide;
        }
        if ($rightSide !== ($rightPlaceholders = self::resolvePlaceholderValue($rightSide))) {
            foreach ($rightPlaceholders as $rightPlaceholder) {
                $this->handlingPlaceholder = $rightSide;
                try {
                    $this->merge($leftSide, $rightPlaceholder);
                } finally {
                    $this->handlingPlaceholder = null;
                }
            }
            return $rightSide;
        }
        $this->doValidateType($leftSide);
        $this->doValidateType($rightSide);
        return $this->mergeValues($leftSide, $rightSide);
    }
    /**
     * {@inheritdoc}
     * @param mixed $value
     * @return mixed
     */
    public final function normalize($value)
    {
        $value = $this->preNormalize($value);
        // run custom normalization closures
        foreach ($this->normalizationClosures as $closure) {
            $value = $closure($value);
        }
        // resolve placeholder value
        if ($value !== ($placeholders = self::resolvePlaceholderValue($value))) {
            foreach ($placeholders as $placeholder) {
                $this->handlingPlaceholder = $value;
                try {
                    $this->normalize($placeholder);
                } finally {
                    $this->handlingPlaceholder = null;
                }
            }
            return $value;
        }
        // replace value with their equivalent
        foreach ($this->equivalentValues as $data) {
            if ($data[0] === $value) {
                $value = $data[1];
            }
        }
        // validate type
        $this->doValidateType($value);
        // normalize value
        return $this->normalizeValue($value);
    }
    /**
     * Normalizes the value before any other normalization is applied.
     * @param mixed $value
     * @return mixed
     */
    protected function preNormalize($value)
    {
        return $value;
    }
    /**
     * Returns parent node for this node.
     */
    public function getParent() : ?\ECSPrefix20211130\Symfony\Component\Config\Definition\NodeInterface
    {
        return $this->parent;
    }
    /**
     * {@inheritdoc}
     * @param mixed $value
     * @return mixed
     */
    public final function finalize($value)
    {
        if ($value !== ($placeholders = self::resolvePlaceholderValue($value))) {
            foreach ($placeholders as $placeholder) {
                $this->handlingPlaceholder = $value;
                try {
                    $this->finalize($placeholder);
                } finally {
                    $this->handlingPlaceholder = null;
                }
            }
            return $value;
        }
        $this->doValidateType($value);
        $value = $this->finalizeValue($value);
        // Perform validation on the final value if a closure has been set.
        // The closure is also allowed to return another value.
        foreach ($this->finalValidationClosures as $closure) {
            try {
                $value = $closure($value);
            } catch (\ECSPrefix20211130\Symfony\Component\Config\Definition\Exception\Exception $e) {
                if ($e instanceof \ECSPrefix20211130\Symfony\Component\Config\Definition\Exception\UnsetKeyException && null !== $this->handlingPlaceholder) {
                    continue;
                }
                throw $e;
            } catch (\Exception $e) {
                throw new \ECSPrefix20211130\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException(\sprintf('Invalid configuration for path "%s": ', $this->getPath()) . $e->getMessage(), $e->getCode(), $e);
            }
        }
        return $value;
    }
    /**
     * Validates the type of a Node.
     *
     * @throws InvalidTypeException when the value is invalid
     * @param mixed $value
     */
    protected abstract function validateType($value);
    /**
     * Normalizes the value.
     * @param mixed $value
     * @return mixed
     */
    protected abstract function normalizeValue($value);
    /**
     * Merges two values together.
     * @param mixed $leftSide
     * @param mixed $rightSide
     * @return mixed
     */
    protected abstract function mergeValues($leftSide, $rightSide);
    /**
     * Finalizes a value.
     * @param mixed $value
     * @return mixed
     */
    protected abstract function finalizeValue($value);
    /**
     * Tests if placeholder values are allowed for this node.
     */
    protected function allowPlaceholders() : bool
    {
        return \true;
    }
    /**
     * Tests if a placeholder is being handled currently.
     */
    protected function isHandlingPlaceholder() : bool
    {
        return null !== $this->handlingPlaceholder;
    }
    /**
     * Gets allowed dynamic types for this node.
     */
    protected function getValidPlaceholderTypes() : array
    {
        return [];
    }
    /**
     * @param mixed $value
     * @return mixed
     */
    private static function resolvePlaceholderValue($value)
    {
        if (\is_string($value)) {
            if (isset(self::$placeholders[$value])) {
                return self::$placeholders[$value];
            }
            foreach (self::$placeholderUniquePrefixes as $placeholderUniquePrefix) {
                if (\strncmp($value, $placeholderUniquePrefix, \strlen($placeholderUniquePrefix)) === 0) {
                    return [];
                }
            }
        }
        return $value;
    }
    /**
     * @param mixed $value
     */
    private function doValidateType($value) : void
    {
        if (null !== $this->handlingPlaceholder && !$this->allowPlaceholders()) {
            $e = new \ECSPrefix20211130\Symfony\Component\Config\Definition\Exception\InvalidTypeException(\sprintf('A dynamic value is not compatible with a "%s" node type at path "%s".', static::class, $this->getPath()));
            $e->setPath($this->getPath());
            throw $e;
        }
        if (null === $this->handlingPlaceholder || null === $value) {
            $this->validateType($value);
            return;
        }
        $knownTypes = \array_keys(self::$placeholders[$this->handlingPlaceholder]);
        $validTypes = $this->getValidPlaceholderTypes();
        if ($validTypes && \array_diff($knownTypes, $validTypes)) {
            $e = new \ECSPrefix20211130\Symfony\Component\Config\Definition\Exception\InvalidTypeException(\sprintf('Invalid type for path "%s". Expected %s, but got %s.', $this->getPath(), 1 === \count($validTypes) ? '"' . \reset($validTypes) . '"' : 'one of "' . \implode('", "', $validTypes) . '"', 1 === \count($knownTypes) ? '"' . \reset($knownTypes) . '"' : 'one of "' . \implode('", "', $knownTypes) . '"'));
            if ($hint = $this->getInfo()) {
                $e->addHint($hint);
            }
            $e->setPath($this->getPath());
            throw $e;
        }
        $this->validateType($value);
    }
}
