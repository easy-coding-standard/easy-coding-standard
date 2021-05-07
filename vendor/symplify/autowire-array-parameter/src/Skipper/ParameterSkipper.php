<?php

namespace Symplify\AutowireArrayParameter\Skipper;

use ReflectionMethod;
use ReflectionParameter;
use ECSPrefix20210507\Symfony\Component\DependencyInjection\Definition;
use Symplify\AutowireArrayParameter\TypeResolver\ParameterTypeResolver;
final class ParameterSkipper
{
    /**
     * Classes that create circular dependencies
     *
     * @var string[]
     * @noRector
     */
    private const DEFAULT_EXCLUDED_FATAL_CLASSES = ['ECSPrefix20210507\\Symfony\\Component\\Form\\FormExtensionInterface', 'ECSPrefix20210507\\Symfony\\Component\\Asset\\PackageInterface', 'ECSPrefix20210507\\Symfony\\Component\\Config\\Loader\\LoaderInterface', 'ECSPrefix20210507\\Symfony\\Component\\VarDumper\\Dumper\\ContextProvider\\ContextProviderInterface', 'ECSPrefix20210507\\EasyCorp\\Bundle\\EasyAdminBundle\\Form\\Type\\Configurator\\TypeConfiguratorInterface', 'ECSPrefix20210507\\Sonata\\CoreBundle\\Model\\Adapter\\AdapterInterface', 'ECSPrefix20210507\\Sonata\\Doctrine\\Adapter\\AdapterChain', 'ECSPrefix20210507\\Sonata\\Twig\\Extension\\TemplateExtension'];
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
     * @param \Symplify\AutowireArrayParameter\TypeResolver\ParameterTypeResolver $parameterTypeResolver
     */
    public function __construct($parameterTypeResolver, array $excludedFatalClasses)
    {
        $this->parameterTypeResolver = $parameterTypeResolver;
        $this->excludedFatalClasses = \array_merge(self::DEFAULT_EXCLUDED_FATAL_CLASSES, $excludedFatalClasses);
    }
    /**
     * @param \ReflectionMethod $reflectionMethod
     * @param \ECSPrefix20210507\Symfony\Component\DependencyInjection\Definition $definition
     * @param \ReflectionParameter $reflectionParameter
     * @return bool
     */
    public function shouldSkipParameter($reflectionMethod, $definition, $reflectionParameter)
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
     * @param \ReflectionParameter $reflectionParameter
     * @return bool
     */
    private function isArrayType($reflectionParameter)
    {
        if ($reflectionParameter->getType() === null) {
            return \false;
        }
        $reflectionParameterType = $reflectionParameter->getType();
        return $reflectionParameterType->getName() === 'array';
    }
}
