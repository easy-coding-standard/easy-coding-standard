<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject;

final class Option
{
    /**
     * @var string
     */
    const INPUT_FORMAT = 'input-format';
    /**
     * @var string
     */
    const OUTPUT_FORMAT = 'output-format';
    /**
     * @var string
     */
    const TARGET_SYMFONY_VERSION = 'target-symfony-version';
    /**
     * @var string
     */
    const DRY_RUN = 'dry-run';
    /**
     * @var string
     */
    const SOURCES = 'sources';
}
