<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\DependencyInjection\CompilerPass;

use ECSPrefix20210526\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use ECSPrefix20210526\Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\EasyCodingStandard\ValueObject\Option;
final class DeprecationWarningCompilerPass implements \ECSPrefix20210526\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface
{
    /**
     * @var array<string, string>
     */
    const DEPRECATED_PARAMETERS = [\Symplify\EasyCodingStandard\ValueObject\Option::SETS => 'Use $containerConfigurator->import(<set>); instead'];
    /**
     * @return void
     */
    public function process(\ECSPrefix20210526\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder)
    {
        $parameterBag = $containerBuilder->getParameterBag();
        foreach (self::DEPRECATED_PARAMETERS as $parameter => $message) {
            if (!$parameterBag->has($parameter)) {
                continue;
            }
            $setsParameters = $parameterBag->get($parameter);
            if ($setsParameters === []) {
                continue;
            }
            $message = \sprintf('The "%s" parameter is deprecated. %s', $parameter, $message);
            \trigger_error($message);
            // to make it noticable
            \sleep(2);
        }
    }
}
