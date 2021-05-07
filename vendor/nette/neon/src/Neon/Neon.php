<?php

namespace ECSPrefix20210507\Nette\Neon;

/**
 * Simple parser & generator for Nette Object Notation.
 * @see https://ne-on.org
 */
final class Neon
{
    const BLOCK = \ECSPrefix20210507\Nette\Neon\Encoder::BLOCK;
    const CHAIN = '!!chain';
    /**
     * Returns value converted to NEON. The flag can be Neon::BLOCK, which will create multiline output.
     * @param int $flags
     * @return string
     */
    public static function encode($value, $flags = 0)
    {
        $encoder = new \ECSPrefix20210507\Nette\Neon\Encoder();
        return $encoder->encode($value, $flags);
    }
    /**
     * Converts given NEON to PHP value.
     * Returns scalars, arrays, DateTimeImmutable and Entity objects.
     * @return mixed
     * @param string $input
     */
    public static function decode($input)
    {
        $decoder = new \ECSPrefix20210507\Nette\Neon\Decoder();
        return $decoder->decode($input);
    }
}
