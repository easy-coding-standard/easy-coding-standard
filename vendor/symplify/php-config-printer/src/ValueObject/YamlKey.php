<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject;

final class YamlKey
{
    /**
     * @var string
     */
    const SERVICES = 'services';
    /**
     * @var string
     */
    const AUTOWIRE = 'autowire';
    /**
     * @var string
     */
    const AUTOCONFIGURE = 'autoconfigure';
    /**
     * @var string
     */
    const RESOURCE = 'resource';
    /**
     * @var string
     */
    const _INSTANCEOF = '_instanceof';
    /**
     * @var string
     */
    const _DEFAULTS = '_defaults';
    /**
     * @var string
     */
    const BIND = 'bind';
    /**
     * @var string
     */
    const IMPORTS = 'imports';
    /**
     * @var string
     */
    const FACTORY = 'factory';
    /**
     * @var string
     */
    const CONFIGURATOR = 'configurator';
    /**
     * @var string
     */
    const IGNORE_ERRORS = 'ignore_errors';
    /**
     * @var string
     */
    const PARAMETERS = 'parameters';
    /**
     * @var string
     */
    const PUBLIC = 'public';
    /**
     * @var string
     */
    const TAGS = 'tags';
    /**
     * @var string
     */
    const ALIAS = 'alias';
    /**
     * @var string
     */
    const CLASS_KEY = 'class';
    /**
     * @return string[]
     */
    public function provideRootKeys() : array
    {
        return [self::PARAMETERS, self::IMPORTS, self::SERVICES];
    }
}
