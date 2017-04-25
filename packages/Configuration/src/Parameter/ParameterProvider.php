<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration\Parameter;

use Nette\DI\Container;
use Symplify\EasyCodingStandard\Configuration\Contract\Parameter\ParameterProviderInterface;

final class ParameterProvider implements ParameterProviderInterface
{
    /**
     * @var mixed[]
     */
    private $parameters = [];

    public function __construct(Container $container)
    {
        $this->parameters = $this->unsetNetteParameters($container->getParameters());
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
    private function unsetNetteParameters(array $parameters): array
    {
        unset(
            $parameters['appDir'], $parameters['wwwDir'],
            $parameters['debugMode'], $parameters['productionMode'],
            $parameters['consoleMode'], $parameters['tempDir']
        );

        return $parameters;
    }
}
