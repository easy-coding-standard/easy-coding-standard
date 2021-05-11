<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210511\Symfony\Component\OptionsResolver\Debug;

use ECSPrefix20210511\Symfony\Component\OptionsResolver\Exception\NoConfigurationException;
use ECSPrefix20210511\Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use ECSPrefix20210511\Symfony\Component\OptionsResolver\OptionsResolver;
/**
 * @author Maxime Steinhausser <maxime.steinhausser@gmail.com>
 *
 * @final
 */
class OptionsResolverIntrospector
{
    private $get;
    public function __construct(\ECSPrefix20210511\Symfony\Component\OptionsResolver\OptionsResolver $optionsResolver)
    {
        $this->get = \Closure::bind(function ($property, $option, $message) {
            /** @var OptionsResolver $this */
            if (!$this->isDefined($option)) {
                throw new \ECSPrefix20210511\Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException(\sprintf('The option "%s" does not exist.', $option));
            }
            if (!\array_key_exists($option, $this->{$property})) {
                throw new \ECSPrefix20210511\Symfony\Component\OptionsResolver\Exception\NoConfigurationException($message);
            }
            return $this->{$property}[$option];
        }, $optionsResolver, $optionsResolver);
    }
    /**
     * @return mixed
     *
     * @throws NoConfigurationException on no configured value
     * @param string $option
     */
    public function getDefault($option)
    {
        $option = (string) $option;
        return ($this->get)('defaults', $option, \sprintf('No default value was set for the "%s" option.', $option));
    }
    /**
     * @return mixed[]
     *
     * @throws NoConfigurationException on no configured closures
     * @param string $option
     */
    public function getLazyClosures($option)
    {
        $option = (string) $option;
        return ($this->get)('lazy', $option, \sprintf('No lazy closures were set for the "%s" option.', $option));
    }
    /**
     * @return mixed[]
     *
     * @throws NoConfigurationException on no configured types
     * @param string $option
     */
    public function getAllowedTypes($option)
    {
        $option = (string) $option;
        return ($this->get)('allowedTypes', $option, \sprintf('No allowed types were set for the "%s" option.', $option));
    }
    /**
     * @return mixed[]
     *
     * @throws NoConfigurationException on no configured values
     * @param string $option
     */
    public function getAllowedValues($option)
    {
        $option = (string) $option;
        return ($this->get)('allowedValues', $option, \sprintf('No allowed values were set for the "%s" option.', $option));
    }
    /**
     * @throws NoConfigurationException on no configured normalizer
     * @param string $option
     * @return \Closure
     */
    public function getNormalizer($option)
    {
        $option = (string) $option;
        return \current($this->getNormalizers($option));
    }
    /**
     * @throws NoConfigurationException when no normalizer is configured
     * @param string $option
     * @return mixed[]
     */
    public function getNormalizers($option)
    {
        $option = (string) $option;
        return ($this->get)('normalizers', $option, \sprintf('No normalizer was set for the "%s" option.', $option));
    }
    /**
     * @return string|\Closure
     *
     * @throws NoConfigurationException on no configured deprecation
     *
     * @deprecated since Symfony 5.1, use "getDeprecation()" instead.
     * @param string $option
     */
    public function getDeprecationMessage($option)
    {
        $option = (string) $option;
        trigger_deprecation('symfony/options-resolver', '5.1', 'The "%s()" method is deprecated, use "getDeprecation()" instead.', __METHOD__);
        return $this->getDeprecation($option)['message'];
    }
    /**
     * @throws NoConfigurationException on no configured deprecation
     * @param string $option
     * @return mixed[]
     */
    public function getDeprecation($option)
    {
        $option = (string) $option;
        return ($this->get)('deprecated', $option, \sprintf('No deprecation was set for the "%s" option.', $option));
    }
}
