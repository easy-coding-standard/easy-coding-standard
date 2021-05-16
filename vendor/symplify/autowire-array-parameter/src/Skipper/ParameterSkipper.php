<?php

namespace ECSPrefix20210516\Symplify\AutowireArrayParameter\Skipper;

use ReflectionMethod;
use ReflectionParameter;
use ECSPrefix20210516\Symfony\Component\DependencyInjection\Definition;
use ECSPrefix20210516\Symplify\AutowireArrayParameter\TypeResolver\ParameterTypeResolver;
final class ParameterSkipper
{
    /**
     * Classes that create circular dependencies
     *
     * @var string[]
     * @noRector
     */
    private const DEFAULT_EXCLUDED_FATAL_CLASSES = ['ECSPrefix20210516\\Symfony\\Component\\Form\\FormExtensionInterface', 'ECSPrefix20210516\\Symfony\\Component\\Asset\\PackageInterface', 'ECSPrefix20210516\\Symfony\\Component\\Config\\Loader\\LoaderInterface', 'ECSPrefix20210516\\Symfony\\Component\\VarDumper\\Dumper\\ContextProvider\\ContextProviderInterface', 'ECSPrefix20210516\\EasyCorp\\Bundle\\EasyAdminBundle\\Form\\Type\\Configurator\\TypeConfiguratorInterface', 'ECSPrefix20210516\\Sonata\\CoreBundle\\Model\\Adapter\\AdapterInterface', 'ECSPrefix20210516\\Sonata\\Doctrine\\Adapter\\AdapterChain', 'ECSPrefix20210516\\Sonata\\Twig\\Extension\\TemplateExtension'];
    /**
     * @var ParameterTypeResolver
     */
    private $parameterTypeResolver;
    /**
     * @var string[]
     */
    private $excludedFatalClasses = [];
    /**
     * @param string[] $excludedFatalClasses
     */
    public function __construct(\ECSPrefix20210516\Symplify\AutowireArrayParameter\TypeResolver\ParameterTypeResolver $parameterTypeResolver, array $excludedFatalClasses)
    {
        $this->parameterTypeResolver = $parameterTypeResolver;
        $this->excludedFatalClasses = \array_merge(self::DEFAULT_EXCLUDED_FATAL_CLASSES, $excludedFatalClasses);
    }
    /**
     * @return bool
     */
    public function shouldSkipParameter(\ReflectionMethod $reflectionMethod, \ECSPrefix20210516\Symfony\Component\DependencyInjection\Definition $definition, \ReflectionParameter $reflectionParameter)
    {
        if (!$this->isArrayType($reflectionParameter)) {
            return \true;
        }
        // already set
        $argumentName = '$' . $reflectionParameter->getName();
        if (isset($definition->getArguments()[$argumentName])) {
            return \true;
        }
        $parameterType = $this->parameterTypeResolver->resolveParameterType($reflectionParameter->getName(), $reflectionMethod);
        if ($parameterType === null) {
            return \true;
        }
        if (\in_array($parameterType, $this->excludedFatalClasses, \true)) {
            return \true;
        }
        if (!\class_exists($parameterType) && !\interface_exists($parameterType)) {
            return \true;
        }
        // prevent circular dependency
        if ($definition->getClass() === null) {
            return \false;
        }
        return \is_a($definition->getClass(), $parameterType, \true);
    }
    /**
     * @return bool
     */
    private function isArrayType(\ReflectionParameter $reflectionParameter)
    {
        if ($reflectionParameter->getType() === null) {
            return \false;
        }
        $reflectionParameterType = $reflectionParameter->getType();
        return $reflectionParameterType->getName() === 'array';
    }
}
