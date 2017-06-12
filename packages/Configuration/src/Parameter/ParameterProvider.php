<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration\Parameter;

use Nette\Utils\Strings;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class ParameterProvider
{
    /**
     * @var string
     */
    private const PARAMETERS_KEY = 'parameters';

    /**
     * @var mixed[]
     */
    private $parameters = [];

    /**
     * @param Container|ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $parameterBag = $container->getParameterBag();
        if (! $parameterBag->has(self::PARAMETERS_KEY)) {
            return;
        }

        $parameters = $parameterBag->get(self::PARAMETERS_KEY);
        $this->parameters = $this->unsetKernelParameters($parameters);
    }

    /**
     * @return mixed[]
     */
    public function provide(): array
    {
        return $this->parameters;
    }

    /**
     * @param mixed[] $parameters
     * @return mixed[]
     */
    private function unsetKernelParameters(array $parameters): array
    {
        foreach ($parameters as $name => $value) {
            if (Strings::startsWith($name, 'kernel')) {
                unset($parameters[$name]);
            }
        }

        return $parameters;
    }
}
