<?php

namespace ECSPrefix20210515\Symplify\Skipper\SkipVoter;

use ECSPrefix20210515\Symplify\Skipper\Contract\SkipVoterInterface;
use ECSPrefix20210515\Symplify\Skipper\Matcher\FileInfoMatcher;
use ECSPrefix20210515\Symplify\Skipper\SkipCriteriaResolver\SkippedMessagesResolver;
use ECSPrefix20210515\Symplify\SmartFileSystem\SmartFileInfo;
final class MessageSkipVoter implements \ECSPrefix20210515\Symplify\Skipper\Contract\SkipVoterInterface
{
    /**
     * @var SkippedMessagesResolver
     */
    private $skippedMessagesResolver;
    /**
     * @var FileInfoMatcher
     */
    private $fileInfoMatcher;
    public function __construct(\ECSPrefix20210515\Symplify\Skipper\SkipCriteriaResolver\SkippedMessagesResolver $skippedMessagesResolver, \ECSPrefix20210515\Symplify\Skipper\Matcher\FileInfoMatcher $fileInfoMatcher)
    {
        $this->skippedMessagesResolver = $skippedMessagesResolver;
        $this->fileInfoMatcher = $fileInfoMatcher;
    }
    /**
     * @param string|object $element
     * @return bool
     */
    public function match($element)
    {
        if (\is_object($element)) {
            return \false;
        }
        return \substr_count($element, ' ') > 0;
    }
    /**
     * @param string $element
     * @return bool
     */
    public function shouldSkip($element, \ECSPrefix20210515\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
    {
        $skippedMessages = $this->skippedMessagesResolver->resolve();
        if (!\array_key_exists($element, $skippedMessages)) {
            return \false;
        }
        // skip regardless the path
        $skippedPaths = $skippedMessages[$element];
        if ($skippedPaths === null) {
            return \true;
        }
        return $this->fileInfoMatcher->doesFileInfoMatchPatterns($smartFileInfo, $skippedPaths);
    }
}
