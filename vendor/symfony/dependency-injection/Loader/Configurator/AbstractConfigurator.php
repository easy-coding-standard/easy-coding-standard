<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ConfigTransformer20210601\Symfony\Component\DependencyInjection\Loader\Configurator;

use ConfigTransformer20210601\Symfony\Component\Config\Loader\ParamConfigurator;
use ConfigTransformer20210601\Symfony\Component\DependencyInjection\Argument\AbstractArgument;
use ConfigTransformer20210601\Symfony\Component\DependencyInjection\Argument\ArgumentInterface;
use ConfigTransformer20210601\Symfony\Component\DependencyInjection\Definition;
use ConfigTransformer20210601\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use ConfigTransformer20210601\Symfony\Component\DependencyInjection\Parameter;
use ConfigTransformer20210601\Symfony\Component\DependencyInjection\Reference;
use ConfigTransformer20210601\Symfony\Component\ExpressionLanguage\Expression;
abstract class AbstractConfigurator
{
    const FACTORY = 'unknown';
    /**
     * @var callable(mixed, bool $allowService)|null
     */
    public static $valuePreProcessor;
    /** @internal */
    protected $definition;
    public function __call(string $method, array $args)
    {
        if (\method_exists($this, 'set' . $method)) {
            return $this->{'set' . $method}(...$args);
        }
        throw new \BadMethodCallException(\sprintf('Call to undefined method "%s::%s()".', static::class, $method));
    }
    public function __sleep()
    {
        throw new \BadMethodCallException('Cannot serialize ' . __CLASS__);
    }
    public function __wakeup()
    {
        throw new \BadMethodCallException('Cannot unserialize ' . __CLASS__);
    }
    /**
     * Checks that a value is valid, optionally replacing Definition and Reference configurators by their configure value.
     *
     * @param mixed $value
     * @param bool  $allowServices whether Definition and Reference are allowed; by default, only scalars and arrays are
     *
     * @return mixed the value, optionally cast to a Definition/Reference
     */
    public static function processValue($value, $allowServices = \false)
    {
        if (\is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = static::processValue($v, $allowServices);
            }
            return self::$valuePreProcessor ? (self::$valuePreProcessor)($value, $allowServices) : $value;
        }
        if (self::$valuePreProcessor) {
            $value = (self::$valuePreProcessor)($value, $allowServices);
        }
        if ($value instanceof \ConfigTransformer20210601\Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator) {
            return new \ConfigTransformer20210601\Symfony\Component\DependencyInjection\Reference($value->id, $value->invalidBehavior);
        }
        if ($value instanceof \ConfigTransformer20210601\Symfony\Component\DependencyInjection\Loader\Configurator\InlineServiceConfigurator) {
            $def = $value->definition;
            $value->definition = null;
            return $def;
        }
        if ($value instanceof \ConfigTransformer20210601\Symfony\Component\Config\Loader\ParamConfigurator) {
            return (string) $value;
        }
        if ($value instanceof self) {
            throw new \ConfigTransformer20210601\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('"%s()" can be used only at the root of service configuration files.', $value::FACTORY));
        }
        switch (\true) {
            case null === $value:
            case \is_scalar($value):
                return $value;
            case $value instanceof \ConfigTransformer20210601\Symfony\Component\DependencyInjection\Argument\ArgumentInterface:
            case $value instanceof \ConfigTransformer20210601\Symfony\Component\DependencyInjection\Definition:
            case $value instanceof \ConfigTransformer20210601\Symfony\Component\ExpressionLanguage\Expression:
            case $value instanceof \ConfigTransformer20210601\Symfony\Component\DependencyInjection\Parameter:
            case $value instanceof \ConfigTransformer20210601\Symfony\Component\DependencyInjection\Argument\AbstractArgument:
            case $value instanceof \ConfigTransformer20210601\Symfony\Component\DependencyInjection\Reference:
                if ($allowServices) {
                    return $value;
                }
        }
        throw new \ConfigTransformer20210601\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Cannot use values of type "%s" in service configuration files.', \get_debug_type($value)));
    }
}
