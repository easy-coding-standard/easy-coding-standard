<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject;

final class SymfonyVersionFeature
{
    /**
     * @var float
     * @see https://symfony.com/blog/new-in-symfony-3-4-services-are-private-by-default
     */
    const PRIVATE_SERVICES_BY_DEFAULT = 3.4;
    /**
     * @var float
     * @see https://github.com/symfony/symfony/pull/36800
     */
    const REF_OVER_SERVICE = 5.1;
}
