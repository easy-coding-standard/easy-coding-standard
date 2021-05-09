<?php

namespace Symplify\Skipper\Skipper;

use Symplify\Skipper\Contract\SkipVoterInterface;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\Skipper\Tests\Skipper\Skipper\SkipperTest
 */
final class Skipper
{
    /**
     * @var string
     */
    const FILE_ELEMENT = 'file_elements';

    /**
     * @var SkipVoterInterface[]
     */
    private $skipVoters = [];

    /**
     * @param SkipVoterInterface[] $skipVoters
     */
    public function __construct(array $skipVoters)
    {
        $this->skipVoters = $skipVoters;
    }

    /**
     * @param string|object $element
     * @return bool
     */
    public function shouldSkipElement($element)
    {
        $fileInfo = new SmartFileInfo(__FILE__);
        return $this->shouldSkipElementAndFileInfo($element, $fileInfo);
    }

    /**
     * @return bool
     */
    public function shouldSkipFileInfo(SmartFileInfo $smartFileInfo)
    {
        return $this->shouldSkipElementAndFileInfo(self::FILE_ELEMENT, $smartFileInfo);
    }

    /**
     * @param string|object $element
     * @return bool
     */
    public function shouldSkipElementAndFileInfo($element, SmartFileInfo $smartFileInfo)
    {
        foreach ($this->skipVoters as $skipVoter) {
            if ($skipVoter->match($element)) {
                return $skipVoter->shouldSkip($element, $smartFileInfo);
            }
        }

        return false;
    }
}
