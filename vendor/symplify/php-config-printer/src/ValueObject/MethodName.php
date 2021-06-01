<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject;

final class MethodName
{
    /**
     * @var string
     */
    const SET = 'set';
    /**
     * @var string
     */
    const ALIAS = 'alias';
    /**
     * @var string
     */
    const SERVICES = 'services';
    /**
     * @var string
     */
    const PARAMETERS = 'parameters';
    /**
     * @var string
     */
    const DEFAULTS = 'defaults';
    /**
     * @var string
     */
    const INSTANCEOF = 'instanceof';
    /**
     * @var string
     */
    const EXTENSION = 'extension';
}
