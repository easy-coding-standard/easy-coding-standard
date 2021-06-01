<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject;

final class VariableName
{
    /**
     * @var string
     */
    const CONTAINER_CONFIGURATOR = 'containerConfigurator';
    /**
     * @var string
     */
    const ROUTING_CONFIGURATOR = 'routingConfigurator';
    /**
     * @var string
     */
    const SERVICES = 'services';
    /**
     * @var string
     */
    const PARAMETERS = 'parameters';
}
