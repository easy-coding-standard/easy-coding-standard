<?php

namespace ECSPrefix20210514\Symplify\Skipper\Skipper;

use ECSPrefix20210514\Symplify\Skipper\Contract\SkipVoterInterface;
use ECSPrefix20210514\Symplify\SmartFileSystem\SmartFileInfo;
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
        $fileInfo = new \ECSPrefix20210514\Symplify\SmartFileSystem\SmartFileInfo(__FILE__);
        return $this->shouldSkipElementAndFileInfo($element, $fileInfo);
    }
    /**
     * @return bool
     */
    public function shouldSkipFileInfo(\ECSPrefix20210514\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
    {
        return $this->shouldSkipElementAndFileInfo(self::FILE_ELEMENT, $smartFileInfo);
    }
    /**
     * @param string|object $element
     * @return bool
     */
    public function shouldSkipElementAndFileInfo($element, \ECSPrefix20210514\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
    {
        foreach ($this->skipVoters as $skipVoter) {
            if ($skipVoter->match($element)) {
                return $skipVoter->shouldSkip($element, $smartFileInfo);
            }
        }
        return \false;
    }
}
