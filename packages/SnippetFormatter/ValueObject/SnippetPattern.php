<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SnippetFormatter\ValueObject;

final class SnippetPattern
{
    /**
     * @see https://regex101.com/r/4YUIu1/9
     * @var string
     */
    public const MARKDOWN_PHP_SNIPPET_REGEX = '#(?<opening>```php\s+)(?<content>[^```]+\n)(?<closing>(\s+)?```)#ms';
}
