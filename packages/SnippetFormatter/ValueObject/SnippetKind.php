<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\SnippetFormatter\ValueObject;

/**
 * @enum
 */
final class SnippetKind
{
    /**
     * @var string
     */
    public const HERE_NOW_DOC = 'herenowdoc';
    /**
     * @var string
     */
    public const MARKDOWN = 'markdown';
}
