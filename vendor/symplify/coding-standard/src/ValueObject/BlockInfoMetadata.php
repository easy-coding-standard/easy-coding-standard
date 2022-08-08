<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\ValueObject;

use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
final class BlockInfoMetadata
{
    /**
     * @var string
     */
    private $blockType;
    /**
     * @var \Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo
     */
    private $blockInfo;
    public function __construct(string $blockType, BlockInfo $blockInfo)
    {
        $this->blockType = $blockType;
        $this->blockInfo = $blockInfo;
    }
    public function getBlockType() : string
    {
        return $this->blockType;
    }
    public function getBlockInfo() : BlockInfo
    {
        return $this->blockInfo;
    }
}
