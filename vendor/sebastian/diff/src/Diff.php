<?php

/*
 * This file is part of sebastian/diff.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\SebastianBergmann\Diff;

final class Diff
{
    /**
     * @var string
     */
    private $from;
    /**
     * @var string
     */
    private $to;
    /**
     * @var Chunk[]
     */
    private $chunks;
    /**
     * @param Chunk[] $chunks
     * @param string $from
     * @param string $to
     */
    public function __construct($from, $to, array $chunks = [])
    {
        $this->from = $from;
        $this->to = $to;
        $this->chunks = $chunks;
    }
    /**
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }
    /**
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }
    /**
     * @return mixed[]
     */
    public function getChunks()
    {
        return $this->chunks;
    }
    /**
     * @param Chunk[] $chunks
     * @return void
     */
    public function setChunks(array $chunks)
    {
        $this->chunks = $chunks;
    }
}
