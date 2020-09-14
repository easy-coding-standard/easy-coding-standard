<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SnippetFormatter\ValueObject;

final class SnippetPattern
{
    /**
     * @see https://regex101.com/r/4YUIu1/4
     * @var string
     */
    public const MARKDOWN_PHP_SNIPPET_PATTERN = '#(?<opening>\`\`\`php\s+)(?<content>[^\`\`\`|^\-\-\-\-\-]+\n)(?<closing>(\s+)?\`\`\`)#ms';

    /**
     * @see https://regex101.com/r/SZr0X5/12
     * @var string
     */
    public const HERENOWDOC_SNIPPET_PATTERN = '#(?<opening><<<(\'?([A-Z]+)\'?|\"?([A-Z]+)\"?)\s+)(?<content>[^\3|\4]+)(?<closing>(\s+)?\3|\4)#msU';
}
