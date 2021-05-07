<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Component\OptionsResolver;

use ECSPrefix20210507\Symfony\Component\OptionsResolver\Exception\AccessException;
final class OptionConfigurator
{
    private $name;
    private $resolver;
    /**
     * @param string $name
     * @param \ECSPrefix20210507\Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function __construct($name, $resolver)
    {
        $this->name = $name;
        $this->resolver = $resolver;
        $this->resolver->setDefined($name);
    }
    /**
     * Adds allowed types for this option.
     *
     * @param string ...$types One or more accepted types
     *
     * @return $this
     *
     * @throws AccessException If called from a lazy option or normalizer
     */
    public function allowedTypes(...$types)
    {
        $this->resolver->setAllowedTypes($this->name, $types);
        return $this;
    }
    /**
     * Sets allowed values for this option.
     *
     * @param mixed ...$values One or more acceptable values/closures
     *
     * @return $this
     *
     * @throws AccessException If called from a lazy option or normalizer
     */
    public function allowedValues(...$values)
    {
        $this->resolver->setAllowedValues($this->name, $values);
        return $this;
    }
    /**
     * Sets the default value for this option.
     *
     * @param mixed $value The default value of the option
     *
     * @return $this
     *
     * @throws AccessException If called from a lazy option or normalizer
     */
    public function default($value)
    {
        $this->resolver->setDefault($this->name, $value);
        return $this;
    }
    /**
     * Defines an option configurator with the given name.
     * @return $this
     * @param string $option
     */
    public function define($option)
    {
        return $this->resolver->define($option);
    }
    /**
     * Marks this option as deprecated.
     *
     * @param string          $package The name of the composer package that is triggering the deprecation
     * @param string          $version The version of the package that introduced the deprecation
     * @param string|\Closure $message The deprecation message to use
     *
     * @return $this
     */
    public function deprecated($package, $version, $message = 'The option "%name%" is deprecated.')
    {
        $this->resolver->setDeprecated($this->name, $package, $version, $message);
        return $this;
    }
    /**
     * Sets the normalizer for this option.
     *
     * @param \Closure $normalizer The normalizer
     *
     * @return $this
     *
     * @throws AccessException If called from a lazy option or normalizer
     */
    public function normalize($normalizer)
    {
        $this->resolver->setNormalizer($this->name, $normalizer);
        return $this;
    }
    /**
     * Marks this option as required.
     *
     * @return $this
     *
     * @throws AccessException If called from a lazy option or normalizer
     */
    public function required()
    {
        $this->resolver->setRequired($this->name);
        return $this;
    }
    /**
     * Sets an info message for an option.
     *
     * @return $this
     *
     * @throws AccessException If called from a lazy option or normalizer
     * @param string $info
     */
    public function info($info)
    {
        $this->resolver->setInfo($this->name, $info);
        return $this;
    }
}
