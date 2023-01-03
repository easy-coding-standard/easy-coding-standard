<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Skipper\Skipper;

use SplFileInfo;
use Symplify\EasyCodingStandard\Skipper\Contract\SkipVoterInterface;
/**
 * @api
 * @see \Symplify\EasyCodingStandard\Tests\Skipper\Skipper\Skipper\SkipperTest
 */
final class Skipper
{
    /**
     * @var string
     */
    private const FILE_ELEMENT = 'file_elements';
    /**
     * @var SkipVoterInterface[]
     * @readonly
     */
    private $skipVoters;
    /**
     * @param SkipVoterInterface[] $skipVoters
     */
    public function __construct(array $skipVoters)
    {
        $this->skipVoters = $skipVoters;
    }
    /**
     * @param string|object $element
     */
    public function shouldSkipElement($element) : bool
    {
        $fileInfo = new SplFileInfo(__FILE__);
        return $this->shouldSkipElementAndFileInfo($element, $fileInfo);
    }
    public function shouldSkipFileInfo(SplFileInfo $fileInfo) : bool
    {
        return $this->shouldSkipElementAndFileInfo(self::FILE_ELEMENT, $fileInfo);
    }
    /**
     * @param string|object $element
     */
    public function shouldSkipElementAndFileInfo($element, SplFileInfo $fileInfo) : bool
    {
        foreach ($this->skipVoters as $skipVoter) {
            if (!$skipVoter->match($element)) {
                continue;
            }
            if (!$skipVoter->shouldSkip($element, $fileInfo)) {
                continue;
            }
            return \true;
        }
        return \false;
    }
}
