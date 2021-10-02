<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Parallel\ValueObject;

use ECSPrefix20211002\React\Stream\ReadableStreamInterface;
/**
 * @see https://github.com/phpstan/phpstan-src/commit/9124c66dcc55a222e21b1717ba5f60771f7dda92#diff-90760a2b132af11ff6b76ee62a9e7bcc1def43830252fb25d96f0c7a0fd9d9aa
 */
final class StreamBuffer
{
    /**
     * @var string
     */
    private $buffer = '';
    public function __construct(\ECSPrefix20211002\React\Stream\ReadableStreamInterface $readableStream)
    {
        $readableStream->on(\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent::DATA, function (string $chunk) : void {
            $this->buffer .= $chunk;
        });
    }
    public function getBuffer() : string
    {
        return $this->buffer;
    }
}
