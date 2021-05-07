<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Component\Cache\Marshaller;

/**
 * Serializes/unserializes PHP values.
 *
 * Implementations of this interface MUST deal with errors carefully. They MUST
 * also deal with forward and backward compatibility at the storage format level.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
interface MarshallerInterface
{
    /**
     * Serializes a list of values.
     *
     * When serialization fails for a specific value, no exception should be
     * thrown. Instead, its key should be listed in $failed.
     * @param mixed[]|null $failed
     * @return mixed[]
     */
    public function marshall(array $values, &$failed);
    /**
     * Unserializes a single value and throws an exception if anything goes wrong.
     *
     * @return mixed
     *
     * @throws \Exception Whenever unserialization fails
     * @param string $value
     */
    public function unmarshall($value);
}
