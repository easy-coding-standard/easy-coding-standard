<?php

declare(strict_types=1);

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
     * @param SkipVoterInterface[] $skipVoters
     */
    public function __construct(
        private readonly array $skipVoters
    ) {
    }

    public function shouldSkipElement(string | object $element): bool
    {
        $fileInfo = new SplFileInfo(__FILE__);
        return $this->shouldSkipElementAndFileInfo($element, $fileInfo);
    }

    public function shouldSkipFileInfo(SplFileInfo $fileInfo): bool
    {
        return $this->shouldSkipElementAndFileInfo(self::FILE_ELEMENT, $fileInfo);
    }

    public function shouldSkipElementAndFileInfo(string | object $element, SplFileInfo $fileInfo): bool
    {
        foreach ($this->skipVoters as $skipVoter) {
            if (! $skipVoter->match($element)) {
                continue;
            }

            if (! $skipVoter->shouldSkip($element, $fileInfo)) {
                continue;
            }

            return true;
        }

        return false;
    }
}
