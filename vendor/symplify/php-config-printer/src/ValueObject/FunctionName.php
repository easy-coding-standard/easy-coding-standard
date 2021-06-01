<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject;

final class FunctionName
{
    /**
     * @var string
     */
    const INLINE_SERVICE = 'ConfigTransformer20210601\\Symfony\\Component\\DependencyInjection\\Loader\\Configurator\\inline_service';
    /**
     * @var string
     */
    const SERVICE = 'ConfigTransformer20210601\\Symfony\\Component\\DependencyInjection\\Loader\\Configurator\\service';
    /**
     * @var string
     */
    const REF = 'ConfigTransformer20210601\\Symfony\\Component\\DependencyInjection\\Loader\\Configurator\\ref';
    /**
     * @var string
     */
    const EXPR = 'ConfigTransformer20210601\\Symfony\\Component\\DependencyInjection\\Loader\\Configurator\\expr';
}
