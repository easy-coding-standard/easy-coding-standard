<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Skipper\SkipVoter;

use Symplify\EasyCodingStandard\Skipper\Contract\SkipVoterInterface;
use Symplify\EasyCodingStandard\Skipper\Matcher\FileInfoMatcher;
use Symplify\EasyCodingStandard\Skipper\SkipCriteriaResolver\SkippedMessagesResolver;
use Symplify\SmartFileSystem\SmartFileInfo;

final class MessageSkipVoter implements SkipVoterInterface
{
    public function __construct(
        private SkippedMessagesResolver $skippedMessagesResolver,
        private FileInfoMatcher $fileInfoMatcher
    ) {
    }

    public function match(string | object $element): bool
    {
        if (is_object($element)) {
            return false;
        }

        return substr_count($element, ' ') > 0;
    }

    public function shouldSkip(string | object $element, SmartFileInfo | string $file): bool
    {
        if (is_object($element)) {
            return false;
        }

        $skippedMessages = $this->skippedMessagesResolver->resolve();
        if (! array_key_exists($element, $skippedMessages)) {
            return false;
        }

        // skip regardless the path
        $skippedPaths = $skippedMessages[$element];
        if ($skippedPaths === null) {
            return true;
        }

        return $this->fileInfoMatcher->doesFileInfoMatchPatterns($file, $skippedPaths);
    }
}
