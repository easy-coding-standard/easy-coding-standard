<?php

namespace Symplify\EasyCodingStandard\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\EasyCodingStandard\ValueObject\Option;

final class DeprecationWarningCompilerPass implements CompilerPassInterface
{
    /**
     * @var array<string, string>
     */
    const DEPRECATED_PARAMETERS = [
        Option::SETS => 'Use $containerConfigurator->import(<set>); instead',
    ];

    /**
     * @return void
     */
    public function process(ContainerBuilder $containerBuilder)
    {
        $parameterBag = $containerBuilder->getParameterBag();

        foreach (self::DEPRECATED_PARAMETERS as $parameter => $message) {
            if (! $parameterBag->has($parameter)) {
                continue;
            }

            $setsParameters = $parameterBag->get($parameter);
            if ($setsParameters === []) {
                continue;
            }

            $message = sprintf('The "%s" parameter is deprecated. %s', $parameter, $message);
            trigger_error($message);
            // to make it noticable
            sleep(2);
        }
    }
}
